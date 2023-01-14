// @flow
import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps} from "mui-datatables";
import {merge, omit, cloneDeep} from "lodash";
import {MuiThemeProvider, Theme, useMediaQuery, useTheme} from "@material-ui/core";

interface TableProps extends MUIDataTableProps {
    columns: TableColumn[];
    loading?: boolean;
}

export interface TableColumn extends MUIDataTableColumn {
    width?: string;

}

const LABEL_NO_MATCH = "Nenhum registro encontrado"

const defaultOtions: MUIDataTableOptions = {
    print: false,
    download: false,
    textLabels: {
        body: {
            noMatch: LABEL_NO_MATCH,
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

    function applyLoading() {
        const textLabels = (newProps.options as any).textLabels
        textLabels.body.noMatch = (newProps.loading === true) ? 'Carregando...' : LABEL_NO_MATCH;
    }

    function applyResponsive() {
        newProps.options.responsive = (isSmOrDown) ? 'standard' : 'vertical';
    }

    function getOriginalMuiDataTableProps() {
        return omit(newProps, 'loading');
    }

    const theme = cloneDeep<Theme>(useTheme());


    const isSmOrDown = useMediaQuery(theme.breakpoints.down('sm'));


    const newProps = merge(
        {options: defaultOtions},
        props,
        {columns: extractMuiDataTableColumns(props.columns)}
    )

    applyLoading();
    applyResponsive();

    const originalProps = getOriginalMuiDataTableProps();

    return (
        <MuiThemeProvider theme={theme}>
            <MUIDataTable {...originalProps} />
        </MuiThemeProvider>
    );
};

export default Table;

export function makeActionStyle(column)
{
    return theme => {
        const copyTheme = cloneDeep(theme);
        const selector = `&[data-testid^="MuiDataTableBodyCell-${column}"]`;
        (copyTheme.overrides as any).MUIDataTableBodyCell.root[selector] = {
            paddingTop: '0px',
            paddingBottom: '0px'
        };
        return copyTheme;
    }
}
