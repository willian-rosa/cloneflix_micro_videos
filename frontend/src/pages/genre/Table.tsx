import * as React from 'react';
import {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import genreHttp from "../../util/http/genre-http";
import {Link} from "react-router-dom";
import EditIcon from "@material-ui/icons/Edit";

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "categories",
        label: "Categorias",
        options: {
            customBodyRender(value, tableMeta, updateValue)
            {
                return value.map((value) => value['name']).join(', ');
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
                return <Link to={`genres/${value}/edit`} >
                    <EditIcon/>
                </Link>

            }
        }
    }
]

const Table = () => {

    const [data, setData] = useState([]);

    useEffect(() => {
        genreHttp.list().then(
            response => {
                setData(response.data['data'])
            }
        )
    }, []);

    return (
        <MUIDataTable
            title=""
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;