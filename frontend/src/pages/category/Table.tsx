import * as React from 'react';
import {useEffect, useState} from 'react';
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


type Props = {

};

const Table = (props: Props) => {

    const snackbar = useSnackbar();
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false)

    useEffect(() => {
        let isActiveComponent = true;
        (async () => {
            setLoading(true);
            try {
                const {data} = await categoryHttp.list<ListResponse<Category>>();
                if (isActiveComponent) {
                    setData(data.data);
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

            return () => {
                isActiveComponent = false;
            }
        })();
    }, []);

    return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinition.length - 1)}>
            <DefaultTable
                title={"Minha tabela"}
                columns={columnsDefinition}
                data={data}
                loading={loading}
            />
        </MuiThemeProvider>
    );
};

export default Table;
