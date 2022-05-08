import * as React from 'react';
import {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {httpVideo} from "../../util/http";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import {Link} from "react-router-dom";
import EditIcon from "@material-ui/icons/Edit";

const CastMemberEnum = {
    1: "Diretor",
    2: "Ator",
}


const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "type",
        label: "Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue)
            {
                return CastMemberEnum[value];
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
                return <Link to={`cast-members/${value}/edit`} >
                    <EditIcon/>
                </Link>

            }
        }
    }
]

type Props = {

};
const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        httpVideo.get('cast_members').then(
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