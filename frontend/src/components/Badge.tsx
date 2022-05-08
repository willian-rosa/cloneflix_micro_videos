import * as React from 'react';
import {Chip, createTheme, MuiThemeProvider} from "@material-ui/core";
import theme from "../theme";

const localTheme = createTheme({
    palette: {
        primary: theme.palette.success,
        secondary: theme.palette.error
    }
});

export const BadgeYes = () => {
    return (
        <MuiThemeProvider theme={localTheme}>
            <Chip label="Ativo" color="primary" />
        </MuiThemeProvider>
    );
};

export const BadgeNo = () => {
    return (
        <MuiThemeProvider theme={localTheme}>
            <Chip label="Inativo" color="secondary" />
        </MuiThemeProvider>
    );
};