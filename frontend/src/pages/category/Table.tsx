import * as React from 'react';
import {useEffect, useState} from 'react';
import {MUIDataTableColumn} from "mui-datatables";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from "../../util/http/category-http";
import {BadgeNo, BadgeYes} from "../../components/Badge";
import EditIcon from '@material-ui/icons/Edit';
import {Link} from "react-router-dom";
import {Category, ListResponse} from "../../util/models";
import DefaultTable, {TableColumn} from "../../components/Table"

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
                return <Link to={`categories/${value}/edit`} >
                    <EditIcon/>
                </Link>

            }
        }
    }
]


type Props = {

};
const Table = (props: Props) => {

    const [data, setData] = useState<Category[]>([]);

    useEffect(() => {
        let isActiveComponent = true;
        (async () => {
            const {data} = await categoryHttp.list<ListResponse<Category>>();
            if (isActiveComponent) {
                console.log(data.data[0])
                setData(data.data);
            }
            return () => {
                isActiveComponent = false;
            }
        })();
    }, []);

    return (
        <DefaultTable
            title={"Minha tabela"}
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;
