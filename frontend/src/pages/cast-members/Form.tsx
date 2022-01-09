import * as React from 'react';
import {
    Box,
    Button,
    ButtonProps,
    FormControl,
    FormControlLabel,
    FormLabel,
    Radio,
    RadioGroup,
    TextField
} from "@material-ui/core";
import {useForm} from "react-hook-form";
import {makeStyles, Theme} from "@material-ui/core/styles";
import castMemberHttp from "../../util/http/cast-member-http";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

// TODO isso está duplicado
const CastMemberEnum = {
    1: "Diretor",
    2: "Ator",
}

export const Form = () => {

    const classes = useStyles();

    const {register, getValues, handleSubmit} = useForm();

    const propsButton: ButtonProps = {
        variant: "contained",
        color: "secondary",
        className: classes.submit
    };

    function onSubmit(formData) {
        castMemberHttp.create(formData)
            .then((response) => console.log(response));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                {...register('name')}
                label="Nome"
                fullWidth
                variant={"outlined"}
            />

            <FormControl component="fieldset" margin="normal">
                <FormLabel component="legend">Tipo</FormLabel>
                <RadioGroup aria-label="Tipo">
                    <FormControlLabel control={<Radio color="primary" />} label="Diretor" value="1" {...register('type')}/>
                    <FormControlLabel control={<Radio color="primary" />} label="Ator" value="2" {...register('type')}/>
                </RadioGroup>
            </FormControl>


            <Box dir="rtl">
                <Button {...propsButton} onClick={() => onSubmit(getValues())}>Salvar</Button>
                <Button {...propsButton} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};