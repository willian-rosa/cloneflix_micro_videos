import * as React from 'react';
import {useEffect, useState} from 'react';
import {Box, Button, ButtonProps, MenuItem, TextField} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../util/http/category-http";
import genreHttp from "../../util/http/genre-http";
import * as yup from "../../util/vendor/yup";
import {useSnackbar} from "notistack";
import {useHistory, useParams} from "react-router";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string().label('Nome').required(),
});

export const Form = () => {
    const snackbar = useSnackbar();
    const history = useHistory();
    const classes = useStyles();
    const buttonProps: ButtonProps = {
        variant: "contained",
        color: "secondary",
        className: classes.submit
    };

    const [categories, setCategories] = useState<any>([]);
    const {register, handleSubmit, getValues, errors, reset, watch, setValue} = useForm({
        validationSchema,
        defaultValues: {
            name: '',
            categories_id: []
        }
    });

    const params: {id?} = useParams();
    const [loading, setLoading] = useState<boolean>(false);
    const [genre, setGenre] = useState<{id: string} | null>(null);

    useEffect(() => {
        register('categories_id')
    }, [register]);

    useEffect(() => {
        categoryHttp
            .list({queryParams: {all: ''}})
            .then(({data}) => setCategories(data.data))
    }, [])

    useEffect(() => {
        if (!params.id) {
            return;
        }
        setLoading(true);
        genreHttp
            .get(params.id)
            .then(({data}) => {
                let genreEdit = data.data;
                genreEdit.categories_id = genreEdit.categories.map((item) => item.id);
                setGenre(genreEdit);
                reset(genreEdit);
            })
            .catch((error) => {
                snackbar.enqueueSnackbar('Erro ao buscar Gênere', {variant: "error"})
                console.log(error);
            })
            .finally(() => setLoading(false))
    }, []);

    function onSubmit(formData, event) {
        setLoading(true);

        const http = !genre
            ? genreHttp.create(formData)
            : genreHttp.update(genre.id, formData)


        http.then((response) => {
            console.log(response)
            snackbar.enqueueSnackbar('Gênere salvo com sucesso', {variant: "success"})
            setTimeout(() => {
                if (event) {
                    if (params.id) {
                        history.replace(`/genres/${response.data.data.id}/edit`)
                    } else {
                        history.push(`/genres/${response.data.data.id}/edit`)
                    }
                } else {
                    history.push('/genres');
                }
            });
        })
        .catch((error) => {
            snackbar.enqueueSnackbar('Erro ao salvar a gênere', {variant: "error"})
            console.log(error)
        })
        .finally(() => setLoading(false));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                inputRef={register()}
                label="Nome"
                margin="normal"
                variant={"outlined"}
                fullWidth
                disabled={loading}
                error={errors.name != undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
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
                disabled={loading}
                // error={errors.categories_id != undefined}
                // helperText={errors.categories_id && errors.categories_id.message}
                InputLabelProps={{shrink: true}}
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
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button type="submit" {...buttonProps}>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};