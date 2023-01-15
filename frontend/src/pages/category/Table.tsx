import * as React from 'react';
import {useEffect, useReducer, useRef, useState} from 'react';
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from "../../util/http/category-http";
import {BadgeNo, BadgeYes} from "../../components/Badge";
import EditIcon from '@material-ui/icons/Edit';
import {Link} from "react-router-dom";
import {Category, ListResponse} from "../../util/models";
import DefaultTable, {makeActionStyle, TableColumn} from "../../components/Table"
import {useSnackbar} from "notistack";
import {IconButton, MuiThemeProvider} from "@material-ui/core";
import {FilterResetButton} from "../../components/Table/FilterResetButton";

interface Pagination {
    page: number;
    total: number;
    per_page: number;
}

interface Order {
    sort: string | null;
    dir: string | null;
}

interface SearchState {
    search?: string;
    pagination: Pagination;
    order: Order
}

const columnsDefinition: TableColumn[] = [
    {
        name: "id",
        label: "ID",
        width: "33%",
        options: {
            sort: false
        }
    },
    {
        name: "name",
        label: "Nome",
        width: "33%"
    },
    {
        name: "is_active",
        label: "Status",
        width: "4%",
        options: {
            customBodyRender(value, tableMeta, updateValue)
            {
                return value ? <BadgeYes /> : <BadgeNo/>
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        width: "10%",
        options: {
            customBodyRender(value, tableMeta, updateValue)
            {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>
            }
        }
    },
    {
        name: "actions",
        label: "Ação",
        options: {
            sort: false,
            customBodyRender(value, tableMeta, updateValue)
            {
                return <IconButton
                        color={'secondary'}
                        component={Link}
                        to={`categories/${tableMeta.rowData[0]}/edit`} >
                    <EditIcon/>
                </IconButton>

            }
        }
    }
]

const INITIAL_STATE = {
    search: '',
    pagination: {
        page: 1,
        total: 0,
        per_page: 10
    },
    order: {
        sort: null,
        dir: null,
    }
};

function reducer(state, action) {
    switch (action.type) {
        case 'search':
            return {
                ...state,
                search: action.value ? action.value : '',
                pagination: {
                    ...state.pagination,
                    page: 1
                }
            }
            break;
        case 'page':
            return {
                ...state,
                pagination: {
                    ...state.pagination,
                    page: action.page
                }
            }
            break;
        case 'per_page':
            return {
                ...state,
                pagination: {
                    ...state.pagination,
                    per_page: action.per_page
                }
            }
            break;
        case 'order':
            return {
                ...state,
                order: {
                    sort: action.changedColumn,
                    dir: action.direction
                }
            }
            break;
        case 'reset':
        default:
            return INITIAL_STATE;
            break;
    }
}


const Table = () => {
    const snackbar = useSnackbar();
    const subscribed = useRef(true);
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [searchState, dispatch] = useReducer(reducer, INITIAL_STATE);

    const columns = columnsDefinition.map(column => {
        if (column.name !== searchState.order.sort) {
            return column;
        }
        return {
            ...column,
            options: {
                ...column.options,
                sortDirection: searchState.order.dir as any
            }
        };
    });

    useEffect(() => {
        subscribed.current = true;
        getData();
        return () => {
            subscribed.current = false;
        };
    }, [
        searchState.search,
        searchState.pagination.page,
        searchState.pagination.per_page,
        searchState.order
    ]);

    async function getData() {
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>(
                {
                    queryParams: {
                        search: cleanSearchText(searchState.search),
                        page: searchState.pagination.page,
                        per_page: searchState.pagination.per_page,
                        sort: searchState.order.sort,
                        dir: searchState.order.dir
                    }
                }
            );
            if (subscribed.current) {
                setData(data.data);
                // setSearchState((prevState => ({
                //     ...prevState,
                //     pagination: {
                //         ...prevState.pagination,
                //         total: data.meta.total
                //     }
                // })));
            }
        } catch (error) {
            console.error(error);
            if (categoryHttp.isCancelledRequest(error)) {
                return;
            }
            snackbar.enqueueSnackbar(
                'Não foi possível carragar as informações',
                {variant: 'error'}
            )
        } finally {
            setLoading(false);
        }
    }

    function cleanSearchText(text) {
        let newText = text;
        if (text && text.value !== undefined) {
            newText = text.value;
        }
        return newText;
    }

    return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinition.length - 1)}>
            <DefaultTable
                title={""}
                columns={columns}
                data={data}
                loading={loading}
                debouncedSearchTime={700}
                options={{
                    serverSide: true,
                    searchText: searchState.search,
                    page: searchState.pagination.page - 1,
                    rowsPerPage: searchState.pagination.per_page,
                    count: searchState.pagination.total,
                    customToolbar: () => (
                        <FilterResetButton handleClick={() => dispatch({type: 'reset'})} />
                    ),
                    onSearchChange: (value) => dispatch({type: 'search', search: value}),
                    onChangePage: (page) => dispatch({type: 'page', page: page + 1}),
                    onChangeRowsPerPage: (per_page) => dispatch({type: 'per_page', per_page: per_page}),
                    onColumnSortChange: (changedColumn: string, direction: 'asc' | 'desc') => dispatch({
                        type: 'order',
                        sort: changedColumn,
                        dir: direction
                    })
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;
