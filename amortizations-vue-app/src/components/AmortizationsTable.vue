<template id="table">
    <Loading v-if="loading"/>
    <div class="flex flex-col" v-else>
        <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <h1 class="p-5 text-3xl text-center text-gop-blue font-semibold">Total Amortizations</h1>
                    <!-- Items per page select -->
                    <div class="px-6 text-lg">
                        <label for="itemsPerPage" class="pl-6 mr-2 text-gop-blue">Items per page:</label>
                        <select id="itemsPerPage" class="px-3 text-center border border-gray-300 rounded-lg" v-model="perPageComputed" @change="fetchAmortizations">
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
                                <th v-for="header in headers" scope="col" class="cursor-pointer text-lg font-semibold text-gop-blue w-1/5 px-2 py-4 text-center" :key="header">
                                    <button @click="sortBy(header)">
                                        {{ formatHeader(header) }}
                                        <span v-if="sort_by === header && order === 'asc'">↑</span>
                                        <span v-if="sort_by === header && order === 'desc'">↓</span>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <!-- table body -->
                        <tbody>
                            <tr class="border-b transition duration-300 ease-in-out hover:bg-gop-lightgreen dark:border-neutral-100 light:hover:bg-neutral-600"
                                v-for="amortization in amortizations"
                                :key="amortization.id"
                            >
                                <td v-for="header in headers"
                                    class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gop-blue text-center"
                                    :key="header"
                                >
                                    {{ amortization[header] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- pagination -->
                    <Pagination 
                        :current-page="currentPage"
                        :total-documents="totalDocuments"
                        :per-page="perPageComputed"
                        @change-page="changePage"
                    >
                    </Pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Pagination from './Pagination.vue';
import { collection, query, getDocs, orderBy, limit, startAfter } from 'firebase/firestore';
import Loading from "./Loading.vue";

export default {
    name: 'AmortizationsTable',
    data() {
        return {
            amortizations: [],
            sort_by: "id",
            order: "asc",
            per_page: 10,
            lastDocument: null,
            prevDocumentStack: [],
            currentPage: 1,
            totalDocuments: 0,
            loading: false,
            headers: ['id', 'schedule_date', 'state', 'amount', 'project_id']
        };
    },
    created() {
        this.fetchAmortizations();
    },
    computed: {
        perPageComputed: {
            get() {
                return this.per_page;
            },
            set(value) {
                this.per_page = Number(value);
            }
    }
    },
    methods: {
        async fetchAmortizations() {
            this.loading = true;

            try {
                let firestoreQuery = collection(this.$db, "amortizations");
                // Build the query
                firestoreQuery = query(
                    firestoreQuery,
                    orderBy(this.sort_by, this.order),
                    limit(this.perPageComputed)
                );

                if (this.lastDocument && this.currentPage !== 1) {
                    firestoreQuery = query(
                        firestoreQuery,
                        startAfter(this.lastDocument)
                    );
                }

                const querySnapshot = await getDocs(firestoreQuery);
                console.log('Number of docs fetched:', querySnapshot.size);
                const data = [];
                querySnapshot.forEach(doc => {
                    console.log('Document data:', doc.data());
                    data.push({ id: doc.id, ...doc.data() });
                });

                if (data.length > 0) {
                    this.lastDocument = querySnapshot.docs[querySnapshot.docs.length - 1];
                    if (this.currentPage > 1) {
                        this.prevDocumentStack.push(this.lastDocument);
                    }
                }

                this.amortizations = data;

                const totalQuery = await getDocs(collection(this.$db, "amortizations"));
                this.totalDocuments = totalQuery.size;
                
            } catch (error) {
                console.error("Error fetching amortizations:", error);
            } finally {
                this.loading = false;
            }
        },
        sortBy(header) {
            if (this.sort_by === header) {
                this.order = this.order === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort_by = header;
                this.order = 'asc';
            }
            this.resetPagination();
            this.fetchAmortizations();
        },
        resetPagination() {
            this.lastDocument = null;
            this.prevDocumentStack = [];
            this.currentPage = 1;
        },
        async changePage(direction) {
            if (direction === "next") {
                this.currentPage++;
                this.fetchAmortizations();
            } else if (direction === "prev" && this.currentPage > 1) {
                this.currentPage--;
                if (this.currentPage !== 1) { // Don't pop for the first page
                    this.lastDocument = this.prevDocumentStack.pop();
                }
                this.fetchAmortizations();
            }
        },
        formatHeader(header) {
            return header
            .split('_')
            .map(word => 
                word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
            )
            .join(' ');
        }
    },
    components: {
        Pagination,
        Loading
    }
};
</script>

<style>
/* Add your custom styles here */
</style>