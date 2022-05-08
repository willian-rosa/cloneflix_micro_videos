import * as React from 'react';
import {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from "../../util/http/category-http";
import {BadgeNo, BadgeYes} from "../../components/Badge";
import EditIcon from '@material-ui/icons/Edit';
import {Link} from "react-router-dom";

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "is_active",
        label: "Status",
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
        options: {
            customBodyRender(value, tableMeta, updateValue)
            {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>
            }
        }
    },
    {
        name: "id",
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

interface Category{
    id: string,
    name: string,
}

type Props = {

};
const Table = (props: Props) => {

    const [data, setData] = useState<Category[]>([]);

    useEffect(() => {
        categoryHttp
            .list<{data: Category[]}>()
            .then((response) => setData(response.data.data))
    }, []);

    return (
        <MUIDataTable
            title={"Minha tabela"}
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;