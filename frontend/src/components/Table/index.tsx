// @flow 
import * as React from 'react';
import MUIDataTable, {MUIDataTableOptions, MUIDataTableProps} from "mui-datatables";
import {merge} from "lodash";

type Props = {

};

const defaultOtions: MUIDataTableOptions = {
    print: false,
    download: false,
    textLabels: {
        body: {
            noMatch: "Nenhum registro encontrado",
            toolTip: "Classificar"
        },
        pagination: {
            next: "Próxima página",
            previous: "Página anterior",
            rowsPerPage: "Por página:",
            displayRows: "de"
        },
        toolbar: {
            search: "Busca",
            downloadCsv: "Download CSV",
            print: "Imprimir",
            viewColumns: "Ver colunas",
            filterTable: "Filtrar Tabela"
        },
        filter: {
            all: "Todos",
            title: "Filtros",
            reset: "Limpar"
        },
        viewColumns: {
            title: "Ver colunas",
            titleAria: "Ver/Esconder Colunas da Tabela"
        },
        selectedRows: {
            text: "registro(s) selecionados",
            delete: "Excluir",
            deleteAria: "Excluir registros selecionados"
        }
    }
}

interface TableProps extends MUIDataTableProps {

}

const Table : React.FC<TableProps>  = (props) => {
    const newProps = merge({options: defaultOtions}, props)
    return (
        <MUIDataTable {...newProps} />
    );
};

export default Table;