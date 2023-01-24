import {Dispatch, Reducer, useReducer, useState} from "react";
import reducer, {Creators, INITIAL_STATE} from "../store/filter";
import {Actions as FilterActions, State as FilterState} from "../store/filter/types";
import {MUIDataTableColumn} from "mui-datatables";

interface FilterManagerOtptions {
    columns: MUIDataTableColumn[];
    rowsPerPage: number;
    rowsPerPageOptions: number[];
    debounceTime: number;
}

export default function useFilter(options: FilterManagerOtptions) {
    const filterManager = new FilterManager(options);

    const [filterState, dispatch] = useReducer<Reducer<FilterState, FilterActions>>(reducer, INITIAL_STATE);
    const [totalRecords, setTotalRecords] = useState<number>(0)

    filterManager.state = filterState;
    filterManager.dispatch = dispatch;

    filterManager.applyOrderIncolumns();

    return {
        columns: filterManager.columns,
        filterManager,
        filterState,
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
    debounceTime: number;

    constructor(options: FilterManagerOtptions) {
        this.columns = options.columns;
        this.rowsPerPage = options.rowsPerPage;
        this.rowsPerPageOptions = options.rowsPerPageOptions;
        this.debounceTime = options.debounceTime;
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

}
