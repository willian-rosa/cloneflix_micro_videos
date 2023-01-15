import * as React from 'react';
import {useEffect, useRef, useState} from 'react';
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

interface Pagination {
    page: number;
    total: number;
    per_page: number;
}

interface SearchState {
    search?: string;
    pagination: Pagination
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
    const [searchState, setSearchState] = useState<SearchState>({
        search: '',
        pagination: {
            page: 1,
            total: 0,
            per_page: 5
        }
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
        searchState.pagination.per_page
    ]);

    async function getData() {
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>(
                {
                    queryParams: {
                        search: searchState.search,
                        page: searchState.pagination.page,
                        per_page: searchState.pagination.per_page
                    }
                }
            );
            if (subscribed.current) {
                setData(data.data);
                setSearchState((prevState => ({
                    ...prevState,
                    pagination: {
                        ...prevState.pagination,
                        total: data.meta.total
                    }
                })));
            }
        } catch (error) {
            console.error(error);
            snackbar.enqueueSnackbar(
                'Não foi possível carragar as informações',
                {variant: 'error'}
            )
        } finally {
            setLoading(false);
        }
    }

    return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinition.length - 1)}>
            <DefaultTable
                title={"Minha tabela"}
                columns={columnsDefinition}
                data={data}
                loading={loading}
                options={{
                    serverSide: true,
                    searchText: searchState.search,
                    page: searchState.pagination.page - 1,
                    rowsPerPage: searchState.pagination.per_page,
                    count: searchState.pagination.total,
                    onSearchChange: (value) => setSearchState((prevState => ({
                        ...prevState,
                        search: value ? value : ''
                        }
                    ))),
                    onChangePage: (page) => setSearchState((prevState => ({
                        ...prevState,
                        pagination: {
                            ...prevState.pagination,
                            page: page + 1
                        }
                    }))),
                    onChangeRowsPerPage: (per_page) => setSearchState((prevState => ({
                        ...prevState,
                        pagination: {
                            ...prevState.pagination,
                            per_page: per_page
                        }
                    })))
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;
