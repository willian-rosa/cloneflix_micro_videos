import {Dispatch, Reducer, useReducer, useState} from "react";
import reducer, {Creators, INITIAL_STATE} from "../store/filter";
import {Actions as FilterActions, State as FilterState} from "../store/filter/types";
import {MUIDataTableColumn} from "mui-datatables";
import {useDebounce} from 'use-debounce'
import {useHistory} from "react-router";
import {History} from 'history';

interface FilterManagerOtptions {
    columns: MUIDataTableColumn[];
    rowsPerPage: number;
    rowsPerPageOptions: number[];
    debounceTime: number;
    history: History;
}

interface UseFilterOptions extends Omit<FilterManagerOtptions, 'history'> {

}

export default function useFilter(options: UseFilterOptions) {
    const history = useHistory();

    const filterManager = new FilterManager({...options, history});
    // Pega o state da URL
    const [filterState, dispatch] = useReducer<Reducer<FilterState, FilterActions>>(reducer, INITIAL_STATE);
    const [debouncedFilterState] = useDebounce(filterState, options.debounceTime);
    const [totalRecords, setTotalRecords] = useState<number>(0);


    filterManager.state = filterState;
    filterManager.dispatch = dispatch;

    filterManager.applyOrderIncolumns();

    return {
        columns: filterManager.columns,
        filterManager,
        filterState,
        debouncedFilterState,
        dispatch,
        totalRecords,
        setTotalRecords
    }
}

export class FilterManager {
    state: FilterState = null as any;
    dispatch: Dispatch<FilterActions> = null as any;
    columns: MUIDataTableColumn[];
    rowsPerPage: number;
    rowsPerPageOptions: number[];
    history: History;

    constructor(options: FilterManagerOtptions) {
        this.columns = options.columns;
        this.rowsPerPage = options.rowsPerPage;
        this.rowsPerPageOptions = options.rowsPerPageOptions;
        this.history = options.history;
    }

    changeSearch (value) {
        this.dispatch(Creators.setSearch({search: value}));
    }

    changePage(page) {
        this.dispatch(Creators.setPage({page: page + 1}));
    }

    changeRowsPerPage(per_page) {
        this.dispatch(Creators.setPerPage({per_page: per_page}));
    }

    changeColumnSort(changedColumn: string, direction: string) {
        this.dispatch(Creators.setOrder({
            sort: changedColumn,
            dir: direction.includes('desc') ? 'desc' : 'asc'
        }));
    }

    applyOrderIncolumns() {
        this.columns = this.columns.map(column => {
            if (column.name !== this.state.order.sort) {
                return column;
            }
            return {
                ...column,
                options: {
                    ...column.options,
                    sortDirection: this.state.order.dir as any
                }
            };
        });
    }

    cleanSearchText(text) {
        let newText = text;
        if (text && text.value !== undefined) {
            newText = text.value;
        }
        return newText;
    }

    pushHistory() {
        const newLocation = {
            pathname: this.history.location.pathname,
            search: "?" + (new URLSearchParams(this.formatSearchParams() as any)),
            state: {
                ...this.state,
                search: this.cleanSearchText(this.state.search)
            }
        };
        this.history.push(newLocation);
    }

    private formatSearchParams() {
        const search = this.cleanSearchText(this.state.search);
        return {
            ...(search && search !== '' && {search: search}),
            ...(this.state.pagination.page !== 1 && {page: this.state.pagination.page}),
            ...(this.state.pagination.per_page !== 15 && {per_page: this.state.pagination.per_page}),
            ...(this.state.order.sort && {sort: this.state.order.sort, dir: this.state.order.dir}),
        }
    }

}
