<script setup>
import { computed, onMounted, ref, watch } from 'vue'

const props = defineProps({
    data: {
        type: Array,
        default: new Array(12).fill(0),
        required: true
    },
    loading_chart: {
        type: Boolean,
        default: false
    }
})

const type = 'bar'
const height = 300
const width = 1000
const categories = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agust', 'Sep', 'Okt', 'Nov', 'Des']

const options = {
    chart: {
        type: type,
        width: width,
        height: height,
        toolbar: { show: true }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '33%',
        }
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: '.7rem',
        },
    },
    stroke: {
        curve: 'smooth',
        width: 1,
    },
    legend: {
        show: true,
        horizontalAlign: 'left',
        position: 'top',
        markers: {
            height: 8,
            width: 8,
            offsetX: -3
        },
        itemMargin: {
            horizontal: 0
        }
    },
    grid: {
        padding: {
            top: 0,
            bottom: -8,
            left: 20,
            right: 20
        }
    },
    xaxis: {
        categories: categories,
        labels: {
            style: {
                fontSize: '13px',
            },
        },
        axisTicks: { show: false },
        axisBorder: { show: false },
        decimalsInFloat: 0,
    },
    yaxis: {
        labels: {
            style: {
                fontSize: '13px',
            },
            formatter: function (val) {
                return val.toFixed(0);
            }
        },
        decimalsInFloat: 0,
    },
    responsive: [
        {
            breakpoint: 1700,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '32%'
                    }
                }
            }
        },
        {
            breakpoint: 1580,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '35%'
                    }
                }
            }
        },
        {
            breakpoint: 1440,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '42%'
                    }
                }
            }
        },
        {
            breakpoint: 1300,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '48%'
                    }
                }
            }
        },
        {
            breakpoint: 1200,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '40%'
                    }
                }
            }
        },
        {
            breakpoint: 1040,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '48%'
                    }
                }
            }
        },
        {
            breakpoint: 991,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '30%'
                    }
                }
            }
        },
        {
            breakpoint: 840,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '35%'
                    }
                }
            }
        },
        {
            breakpoint: 768,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '28%'
                    }
                }
            }
        },
        {
            breakpoint: 640,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '32%'
                    }
                }
            }
        },
        {
            breakpoint: 576,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '37%'
                    }
                }
            }
        },
        {
            breakpoint: 480,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '45%'
                    }
                }
            }
        },
        {
            breakpoint: 420,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '52%'
                    }
                }
            }
        },
        {
            breakpoint: 380,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '60%'
                    }
                }
            }
        }
    ],
    states: {
        hover: {
            filter: {
                type: 'none'
            }
        },
        active: {
            filter: {
                type: 'none'
            }
        }
    }
}

const series = computed(() => {
    return [
        {
            name: 'Surat Masuk',
            data: props.data
        }
    ]

})

const value_is_zero = computed(() => {
    return props.data.every((item) => item === 0)
})

</script>
<template>
    <h4 v-if="loading_chart" class="text-center">Loading ...</h4>
    <template v-else>
        <apexchart v-if="!value_is_zero" :options="options" :series="series"></apexchart>
        <h4 v-else class="text-center">Tidak ada surat ditahun ini</h4>
    </template>
</template>