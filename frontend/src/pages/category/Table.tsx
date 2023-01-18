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
import reducer, {INITIAL_STATE, Creators} from "../../store/filter";
import useFilter from "../../hooks/useFilter";

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

const Table = () => {
    const snackbar = useSnackbar();
    const subscribed = useRef(true);
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const {filterState, dispatch, totalRecords, setTotalRecords} = useFilter();

    const columns = columnsDefinition.map(column => {
        if (column.name !== filterState.order.sort) {
            return column;
        }
        return {
            ...column,
            options: {
                ...column.options,
                sortDirection: filterState.order.dir as any
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
        filterState.search,
        filterState.pagination.page,
        filterState.pagination.per_page,
        filterState.order
    ]);

    async function getData() {
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>(
                {
                    queryParams: {
                        search: cleanSearchText(filterState.search),
                        page: filterState.pagination.page,
                        per_page: filterState.pagination.per_page,
                        sort: filterState.order.sort,
                        dir: filterState.order.dir
                    }
                }
            );
            if (subscribed.current) {
                setData(data.data);
                setTotalRecords(data.meta.total);
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
                    searchText: filterState.search as any,
                    page: filterState.pagination.page - 1,
                    rowsPerPage: filterState.pagination.per_page,
                    count: totalRecords,
                    customToolbar: () => (
                        <FilterResetButton handleClick={() => dispatch(Creators.setReset())} />
                    ),
                    onSearchChange: (value) => dispatch(Creators.setSearch({search: value})),
                    onChangePage: (page) => dispatch(Creators.setPage({page: page + 1})),
                    onChangeRowsPerPage: (per_page) => dispatch(Creators.setPerPage({per_page: per_page})),
                    onColumnSortChange: (changedColumn: string, direction: string) => dispatch(Creators.setOrder({
                        sort: changedColumn,
                        dir: direction.includes('desc') ? 'desc' : 'asc'
                    }))
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;
