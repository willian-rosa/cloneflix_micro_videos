import * as React from 'react';
import {Box, Button, ButtonProps} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

interface SubmitActionsProps {
    disableButtons?: boolean;
    handleSave: () => void;
}

const SubmitActions: React.FC<SubmitActionsProps> = (props) => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        variant: "contained",
        color: "secondary",
        className: classes.submit,
        disabled: props.disableButtons !== undefined ? false : props.disableButtons
    };

    return (
        <Box dir={"rtl"}>
            <Button color="primary" {...buttonProps} onClick={props.handleSave}>Salvar</Button>
            <Button type="submit" {...buttonProps}>Salvar e continuar editando</Button>
        </Box>
    );
};

export default SubmitActions;