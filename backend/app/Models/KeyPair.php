<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class KeyPair extends Model
{
    use HasFactory;

    private static function generateKeyPair()
    {
        // $config = [
        //     'private_key_bits' => 2048,
        //     'private_key_type' => OPENSSL_KEYTYPE_RSA,
        // ];

        $resource = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        openssl_pkey_export($resource, $privateKey);

        $publicKey = openssl_pkey_get_details($resource)['key'];

        return [
            'private_key' => $privateKey,
            'public_key' => $publicKey,
        ];
    }

    public static function storeKeys($user_id, $password)
    {
        // $user = User::find($user_id);
        // if (!$user && !Hash::check($password, $user->password)) {
        //     return false;
        // }

        DB::beginTransaction();

        try {
            $new_key_pair = new KeyPair();
            $new_key_pair->user_id = $user_id;

            $keys = self::generateKeyPair();
            $new_key_pair->public_key = $keys['public_key'];
            $key = Hash::make($password);
            Crypt::setKey($key);
            $encryptedPrivateKey = Crypt::encryptString($keys['private_key']);

            $new_key_pair->save();
            Storage::put("private_keys/{$new_key_pair->id}.key", $encryptedPrivateKey);
            // Storage::put("private_keys/iv_{$new_key_pair->id}.txt", base64_encode($iv));

            DB::commit();
            return $new_key_pair;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    function encrypt($password, $data)
    {
        // Retrieve encrypted private key
        $encryptedPrivateKey = Storage::get("private_keys/{$this->id}.key");
        $key = Hash::make($password);
        Crypt::setKey($key);
        // Decrypt private key
        $privateKey = Crypt::decryptString($encryptedPrivateKey);
        return $privateKey;
        // Encrypt the data using the private key
        openssl_private_encrypt($data, $encryptedData, $privateKey);
        // Encrypted data is now in $encryptedData
        return base64_encode($encryptedData);
    }
}
