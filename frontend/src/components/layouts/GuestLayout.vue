<template>
    <main class="bg-gray-100 flex h-full items-center py-16">
        <div class="w-full mx-auto p-6">
            <slot></slot>
        </div>
    </main>
</template>

<style scoped>
main {
    min-height: 100vh;
}
</style>