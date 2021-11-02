import * as React from 'react';
import {
    Box,
    Button,
    ButtonProps,
    FormControl, FormLabel,
    InputLabel,
    MenuItem,
    OutlinedInput,
    Select,
    TextField
} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import {useEffect, useState} from "react";
import categoryHttp from "../../util/http/category-http";
import genreHttp from "../../util/http/genre-http";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

export const Form = () => {
    const classes = useStyles();
    const buttonProps: ButtonProps = {
        variant: "outlined",
        className: classes.submit
    };

    const [categories, setCategories] = useState<any>([]);
    const {register, handleSubmit, getValues, watch, setValue} = useForm({
        defaultValues: {
            name: '',
            categories_id: [],
        }
    });

    useEffect(() => {
        register('categories_id')
    }, [register]);

    useEffect(() => {
        categoryHttp
            .list()
            .then(({data}) => setCategories(data.data))
    }, [])


    function onSubmit(formData) {
        genreHttp
            .create(formData)
            .then((res) => console.log(res));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                {...register('name')}
                label="Nome"
                margin="normal"
                variant={"outlined"}
                fullWidth
            />

            <TextField
                select
                name="categories_id"
                value={watch('categories_id')}
                label="Categorias"
                margin="normal"
                variant={"outlined"}
                fullWidth
                onChange={(e: any) => {
                    setValue('categories_id', e.target.value)
                }}
                SelectProps={{
                    multiple: true
                }}
            >
                <MenuItem value="" disabled>
                    <em>Selecione categorias</em>
                </MenuItem>

                {
                    categories.map
                        ((category, key) => (
                            <MenuItem key={key} value={category.id}>{category.name}</MenuItem>
                        )
                    )
                }
            </TextField>




            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues())}>Salvar</Button>
                <Button type="submit" {...buttonProps}>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};