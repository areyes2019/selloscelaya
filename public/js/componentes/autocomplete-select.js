export default {
  name: 'AutocompleteSelect',
  props: {
    modelValue: [String, Number, Object],
    options: {
      type: Array,
      required: true
    },
    placeholder: {
      type: String,
      default: 'Buscar...'
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      search: '',
      showList: false,
      highlightedIndex: -1
    };
  },
  computed: {
    filteredOptions() {
      const searchTerm = this.search.toLowerCase();
      return this.options.filter(option =>
        option.nombre_completo.toLowerCase().includes(searchTerm)
      );
    }
  },
  methods: {
    selectOption(option) {
      this.$emit('update:modelValue', option.id_articulo);
      this.search = option.nombre_completo;
      this.showList = false;
      this.highlightedIndex = -1;
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.showList = false;
      }
    },
    onKeyDown(event) {
      if (!this.showList) return;

      if (event.key === 'ArrowDown') {
        event.preventDefault();
        if (this.highlightedIndex < this.filteredOptions.length - 1) {
          this.highlightedIndex++;
          this.scrollHighlightedIntoView();
        }
      } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        if (this.highlightedIndex > 0) {
          this.highlightedIndex--;
          this.scrollHighlightedIntoView();
        }
      } else if (event.key === 'Enter') {
        event.preventDefault();
        if (this.highlightedIndex >= 0) {
          const selected = this.filteredOptions[this.highlightedIndex];
          if (selected) this.selectOption(selected);
        }
      } else if (event.key === 'Escape') {
        this.showList = false;
      }
    },
    scrollHighlightedIntoView() {
      this.$nextTick(() => {
        const list = this.$el.querySelector('.autocomplete-list');
        const item = list?.children[this.highlightedIndex];
        if (item) {
          item.scrollIntoView({ block: 'nearest' });
        }
      });
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  template: `
    <div class="autocomplete-select" style="position: relative; max-width: 300px;">
      <input 
        type="text" 
        v-model="search" 
        :placeholder="placeholder" 
        @focus="showList = true" 
        @keydown="onKeyDown"
        class="autocomplete-input"
        style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
      />
      <ul 
        v-if="showList" 
        class="autocomplete-list"
        style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; z-index: 1000; list-style: none; padding: 0; margin: 0;"
      >
        <li v-if="filteredOptions.length === 0" style="padding: 8px; color: #888;">
          No se encontró el artículo que usted busca
        </li>
        <li 
          v-for="(option, index) in filteredOptions" 
          :key="option.id_articulo" 
          @click="selectOption(option)" 
          :style="{
            padding: '8px',
            cursor: 'pointer',
            backgroundColor: index === highlightedIndex ? '#667eea' : 'white',
            color: index === highlightedIndex ? 'white' : 'black'
          }"
        >
          {{ option.nombre_completo }}
        </li>
      </ul>
    </div>
  `
};
