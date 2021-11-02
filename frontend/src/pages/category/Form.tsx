import * as React from 'react';
import {Box, Button, ButtonProps, Checkbox, TextField} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../util/http/category-http";

const useStyles = makeStyles((theme: Theme) => {
   return {
       submit: {
           margin: theme.spacing(1)
       }
   }
});

export const Form = () => {

    const classes = useStyles()

    const buttonProps: ButtonProps = {
        variant: "outlined",
        className: classes.submit
    };

    const {register, handleSubmit, getValues} = useForm({
        defaultValues: {
            is_active: true,
            name: '',
            description: '',
        }
    });

    function onSubmit(formData) {
        categoryHttp
            .create(formData)
            .then((response) => console.log(response));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                {...register('name')}
                label="nome"
                fullWidth
                variant={"outlined"}

            />
            <TextField
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                {...register('description')}
            />

            <Checkbox
                {...register('is_active')}
                defaultChecked
            />
            Ativo?
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues())}>Salvar</Button>
                <Button type="submit" {...buttonProps}>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};