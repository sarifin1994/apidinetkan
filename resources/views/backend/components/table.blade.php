<div x-load x-data="datatable({
    url: '{{ $url }}',
    columns: {{ $columns }},
    initialSort: '{{ $initialSort }}',
    perPage: {{ $perPage }},
})" class="datatable-container">

  <div class="controls d-flex justify-content-between mb-3">
    <select x-model="perPage" class="form-select w-auto">
      <template x-for="option in [10, 25, 50, 100]">
        <option :value="option" x-text="`${option} items`"></option>
      </template>
    </select>

    <div class="d-flex">
      <input x-model="search" type="text" placeholder="Search..." class="form-control w-auto" />

      <div class="dropdown ms-2">
        <button class="btn btn-secondary dropdown-toggle" @click="toggleDropdown">
          Columns
        </button>
        <ul x-show="dropdownOpen" @click.away="dropdownOpen = false" class="dropdown-menu">
          <template x-for="(column, index) in columns">
            <li class="dropdown-item">
              <label>
                <input type="checkbox" :checked="column.visible" @change="toggleColumn(index)">
                <span x-text="column.label"></span>
              </label>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="table-responsive custom-scrollbar">
    <table class="table-striped table">
      <thead>
        <tr>
          <template x-for="(column, index) in columns">
            <th x-show="column.visible" :style="{ width: column.width }" @click="sort(column.key)" class="text-nowrap">
              <span x-text="column.label"></span>
              <span class="sort-icon" x-show="column.key === sortBy"
                x-bind:class="{
                    'asc': sortDirection === 'asc',
                    'desc': sortDirection === 'desc'
                }"></span>
            </th>
          </template>
        </tr>
      </thead>
      <tbody>
        <template x-for="row in filteredData">
          <tr>
            <template x-for="column in columns">
              <td x-show="column.visible" :style="{ width: column.width }" class="text-nowrap"
                x-html="renderColumnContent(row, column)"></td>
            </template>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-between mt-3">
    <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-primary">
      Previous
    </button>

    <div class="pagination">
      <template x-for="page in pages">
        <button @click="currentPage = page"
          :class="{
              'btn btn-outline-primary active': currentPage === page,
              'btn btn-outline-primary': currentPage !== page,
          }"
          x-text="page"></button>
      </template>
    </div>

    <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-primary">
      Next
    </button>
  </div>
</div>
