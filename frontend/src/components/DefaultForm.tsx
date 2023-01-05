import * as React from 'react';
import {Grid, GridProps} from "@material-ui/core";

interface DefaultFormProps extends React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> {
    GridContainerProps?: GridProps;
    GridItemProps?: GridProps;
}

export const DefaultForm: React.FC<DefaultFormProps> = (props) => {
    const {GridContainerProps, GridItemProps, ...other} = props;
    return (
        <form {...other} >
            <Grid container {...GridContainerProps}>
                <Grid item {...GridItemProps}>
                    {props.children}
                </Grid>
            </Grid>
        </form>
    );
};