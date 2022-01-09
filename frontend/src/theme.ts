import {createMuiTheme, SimplePaletteColorOptions} from "@material-ui/core";
import {PaletteOptions} from "@material-ui/core/styles/createPalette";
import {green, red} from "@material-ui/core/colors";

const palette: PaletteOptions = {
    primary: {
        main: "#79aec8",
        contrastText: "#fff"
    },
    secondary: {
        main: "#4db5ab",
        contrastText: "#fff",
        dark: "#055a52"
    },
    background: {
        default: "#fafafa"
    },
    success: {
        main: green['500'],
        contrastText: "#fff"
    },
    error: {
        main: red['400']
    }
}

const theme = createMuiTheme({
    palette,
    overrides: {
        MUIDataTable: {
            paper: {
                boxShadow: "none"
            }
        },
        MUIDataTableToolbar: {
            root: {
                minHeight: '58px',
                backgroundColor: palette!.background!.default,
            },
            icon: {
                color: (palette!.primary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &.focus': {
                    color: (palette!.secondary as SimplePaletteColorOptions).dark
                }
            },
            iconActive: {
                color: (palette!.secondary as SimplePaletteColorOptions).dark,
                '&:hover, &:active, &.focus': {
                    color: (palette!.secondary as SimplePaletteColorOptions).dark
                }
            }
        },
        MUIDataTableHeadCell: {
            fixedHeader: {
                paddingTop: 8,
                paddingBottom: 8,
                backgroundColor: (palette!.primary as SimplePaletteColorOptions).main,
                color: "#ffffff",
                "&[aria-sort]": {
                    backgroundColor: "#459ac4"
                }
            },
            sortAction: {
                color: "#ffffff"
            },
            sortActive: {
                color: "#ffffff"
            },
            sortLabelRoot: {
                '& svg': {
                    color: '#fff !important'
                }
            }
        },
        MUIDataTableSelectCell: {
            headerCell: {
                backgroundColor: (palette!.primary as SimplePaletteColorOptions).main,
                "& span": {
                    color: "#fff"
                }
            }
        },

        MUIDataTableBodyCell: {
            root: {
                color: (palette!.secondary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &.focus': {
                    color: (palette!.secondary as SimplePaletteColorOptions).main
                }
            }
        },
        MUIDataTableToolbarSelect: {
            title: {
                color: (palette!.primary as SimplePaletteColorOptions).main
            },
            iconButton: {
                color: (palette!.primary as SimplePaletteColorOptions).main
            }
        },
        MUIDataTableBodyRow: {
            root: {
                '&:nth-child(odd)': {
                    backgroundColor: palette.background!.default
                }
            }
        },
        MuiTablePagination: {
            root: {
                color: (palette!.primary as SimplePaletteColorOptions).main
            }
        }
    }
});

export default theme;