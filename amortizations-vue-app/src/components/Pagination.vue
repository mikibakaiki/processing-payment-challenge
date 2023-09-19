<template id="pagination">
  <div class="flex items-center justify-between border-t border-gray-200 bg-inherit px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <a class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gop-blue hover:bg-gray-100">Previous</a>
      <a class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gop-blue hover:bg-gray-100">Next</a>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gop-blue">
          Showing
          {{ ' ' }}
          <span class="font-medium">{{ data.from }}</span>
          {{ ' ' }}
          to
          {{ ' ' }}
          <span class="font-medium">{{data.to}}</span>
          {{ ' ' }}
          of
          {{ ' ' }}
          <span class="font-medium">{{data.total}}</span>
          {{ ' ' }}
          results
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <a @click="changePage(data.first_page)"
            :class="!data.prev_page_url ? 'pointer-events-none relative inline-flex items-center rounded-l-md px-3 py-3 text-gop-blue ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 w-12' : 'cursor-pointer relative inline-flex items-center rounded-l-md px-3 py-3 text-gop-blue ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 w-12'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>
          </a>
          <a
        v-for="page in pages"
        :key="page"
        :class="page === data.current_page ? 'cursor-pointer relative z-10 inline-flex items-center bg-gop-yellow/75 p-3 py-2 text-sm font-semibold text-white ring-1 ring-inset ring-gray-300 focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:text-gop-blue w-12 justify-center' : 'cursor-pointer relative inline-flex items-center p-3 py-2 text-sm font-semibold text-gop-blue ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 w-12 justify-center'"
        @click.prevent="changePage(page)"
      >
        {{ page }}
      </a>
          <a @click="changePage(data.last_page)"
            :class="!data.next_page_url ? 'pointer-events-none relative inline-flex items-center rounded-r-md px-3 py-3 text-gop-blue ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 w-12' : 'cursor-pointer relative inline-flex items-center rounded-r-md px-3 py-3 text-gop-blue ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 w-12'"
          >
            <svg  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: ['data'],
  computed: {
    pages() {
      const range = 3; // number of pages to display before and after the current page
      const totalPages = this.data.last_page;
      const currentPage = this.data.current_page;

      let startPage = Math.max(1, currentPage - range);
      let endPage = Math.min(totalPages, currentPage + range);

      // Ensure at least `2 * range + 1` pages are shown
      if (currentPage <= range + 1) {
        endPage = Math.min(totalPages, 2 * range + 1);
      } else if (totalPages - currentPage < range) {
        startPage = Math.max(1, totalPages - 2 * range);
      }

      return Array.from({ length: endPage - startPage + 1 }, (v, k) => k + startPage);
    },
  },
  methods: {
    changePage(page) {
      console.log("PAGE", page);
      console.log(`change-page${this.data.path}?page=${page}`)
      this.$emit('change-page', this.data.path + '?page=' + page);
    },
  },
};
</script>


