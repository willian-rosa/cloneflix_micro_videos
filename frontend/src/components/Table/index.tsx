// @flow
import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps} from "mui-datatables";
import {merge, omit, cloneDeep} from "lodash";
import {MuiThemeProvider, Theme, useTheme} from "@material-ui/core";

export interface TableColumn extends MUIDataTableColumn {
    width?: string;

}

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
    columns: TableColumn[];
}

const Table : React.FC<TableProps>  = (props) => {

    function extractMuiDataTableColumns(columns: TableColumn[]): MUIDataTableColumn[] {
        setColumnsWidth(columns);
        return columns.map(column => omit(column, 'width'));
    }

    function setColumnsWidth(columns: TableColumn[]) {
        columns.forEach((column, key) => {
            if (column.width) {
                const overrrides = theme.overrides as any;
                overrrides.MUIDataTableHeadCell.fixedHeader[`&:nth-child(${key + 2})`] = {
                    width: column.width
                }
            }
        })
    }

    const theme = cloneDeep<Theme>(useTheme());
    const newProps = merge(
        {options: defaultOtions},
        props,
        {columns: extractMuiDataTableColumns(props.columns)}
    )

    return (
        <MuiThemeProvider theme={theme}>
            <MUIDataTable {...newProps} />
        </MuiThemeProvider>
    );
};

export default Table;
