<template id="table">
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <h1 class="p-5 text-3xl text-center text-gop-blue font-semibold">Total Amortizations</h1>
                    <!-- Items per page select -->
                    <div class="px-6 text-lg">
                        <label for="itemsPerPage" class="pl-6 mr-2 text-gop-blue">Items per page:</label>
                        <select id="itemsPerPage" class="px-3 text-center border border-gray-300 rounded-lg" v-model="per_page" @change="fetchAmortizations">
                            <option class="text-gop-blue text-center" value="10">10</option>
                            <option class="text-gop-blue" value="20">20</option>
                            <option class="text-gop-blue" value="50">50</option>
                            <option class="text-gop-blue" value="100">100</option>
                        </select>
                    </div>
                    <table class="min-w-full">
                        <thead class="bg-inherit border-b">
                        <!-- table header -->
                            <tr>
                                <th scope="col" class="cursor-pointer text-lg font-semibold text-gop-blue w-1/5 px-2 py-4 text-center" @click="sortBy('id')">ID 
                                    <span class="inline-block text-center content-center">
                                        <svg class="w-4 h-4 inline-block align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                        </svg>
                                    </span>
                                </th>
                                <th scope="col" class="cursor-pointer text-lg font-semibold text-gop-blue w-1/5 px-2 py-4 text-center" @click="sortBy('schedule_date')">Schedule Date
                                    <span class="inline-block text-center content-center">
                                        <svg class="w-4 h-4 inline-block align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                        </svg>
                                    </span>
                                </th>
                                <th scope="col" class="cursor-pointer text-lg font-semibold text-gop-blue w-1/5 px-2 py-4 text-center" @click="sortBy('state')">State
                                <span class="inline-block text-center content-center">
                                        <svg class="w-4 h-4 inline-block align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                        </svg>
                                    </span>
                                </th>
                                <th scope="col" class="cursor-pointer text-lg font-semibold text-gop-blue w-1/5 px-2 py-4 text-center" @click="sortBy('amount')">Amount
                                <span class="inline-block text-center content-center">
                                        <svg class="w-4 h-4 inline-block align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                        </svg>
                                    </span>
                                </th>
                                <th scope="col" class="cursor-pointer text-lg font-semibold text-gop-blue w-1/5 px-2 py-4 text-center" @click="sortBy('project_id')">Project ID
                                <span class="inline-block text-center content-center">
                                        <svg class="w-4 h-4 inline-block align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                        </svg>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <!-- table body -->
                        <tbody>
                            <tr class="border-b transition duration-300 ease-in-out hover:bg-gop-lightgreen dark:border-neutral-100 light:hover:bg-neutral-600"
                                v-for="amortization in amortizations.data"
                                :key="amortization.id"
                            >
                                <td class="w-1/5 px-2 py-4 whitespace-nowrap text-sm font-medium text-gop-blue text-center">{{ amortization.id }}</td>
                                <td class="w-1/5 px-2 py-4 whitespace-nowrap text-sm font-medium text-gop-blue text-center">{{ amortization.schedule_date }}</td>
                                <td class="w-1/5 px-2 py-4 whitespace-nowrap text-sm font-medium text-gop-blue text-center">{{ amortization.state }}</td>
                                <td class="w-1/5 px-2 py-4 whitespace-nowrap text-sm font-medium text-gop-blue text-center">{{ amortization.amount }}</td>
                                <td class="w-1/5 px-2 py-4 whitespace-nowrap text-sm font-medium text-gop-blue text-center">{{ amortization.project_id }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- pagination -->
                    <pagination 
                        :data="amortizations"
                        @change-page="changePage"
                    >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            amortizations: {},
            sort_by: "id",
            order: "asc",
            per_page: 10,
        };
    },
    created() {
        this.fetchAmortizations();
    },
    methods: {
        fetchAmortizations() {
            axios
                .get("/api/v1/index", {
                    params: {
                        sort_by: this.sort_by,
                        order: this.order,
                        per_page: this.per_page,
                    },
                })
                .then((response) => {
                    this.amortizations = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        sortBy(column) {
            this.sort_by = column;
            this.order = this.order === "asc" ? "desc" : "asc";
            this.fetchAmortizations();
        },
        changePage(url) {
            if (url) {
                axios
                    .get(`${url}&per_page=${this.per_page}&order=${this.order}&sort_by=${this.sort_by}`)
                    .then((response) => {
                        this.amortizations = response.data;
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            }
        },
    },
};
</script>

<style>
/* Add your custom styles here */
</style>