import * as React from 'react';
import {Box, Button, ButtonProps, Checkbox, FormControlLabel, Grid, TextField} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../util/http/category-http";
import * as yup from '../../util/vendor/yup';
import {useParams, useHistory} from "react-router";
import {useEffect, useState} from "react";
import {useSnackbar} from "notistack";
import SubmitActions from "../../components/SubmitActions";
import {DefaultForm} from "../../components/DefaultForm";


const validationSchema = yup.object().shape({
    name: yup.string().label('Nome').required()
});


export const Form = () => {
    const {register, handleSubmit, getValues, errors, reset, watch, setValue, triggerValidation} = useForm({
        validationSchema,
        defaultValues: {
            is_active: true,
            name: '',
            description: '',
        }
    });
    const snackbar = useSnackbar();
    const history = useHistory();
    const params: {id?} = useParams();
    const [category, setCategory] = useState<{id: string} | null>(null);
    const [loading, setLoading] = useState<boolean>(false);


    useEffect(() => {
        register({name: 'is_active'})
    }, [register]);

    useEffect(() => {
        if (!params.id) {
            return;
        }
        setLoading(true);
        categoryHttp
            .get(params.id)
            .then(({data}) => {
                setCategory(data.data);
                reset(data.data);
            })
            .catch((error) => {
                snackbar.enqueueSnackbar('Erro ao buscar categoria', {variant: "error"})
                console.log(error);
            })
            .finally(() => setLoading(false))
    }, []);

    function onSubmit(formData, event) {

        setLoading(true);

        const http = !category
            ? categoryHttp.create(formData)
            : categoryHttp.update(category.id, formData)

            http
                .then(({data}) => {
                    snackbar.enqueueSnackbar('Categoria salva com sucesso', {variant: "success"})
                    setTimeout(() => {
                        if (event) {
                            if (params.id) {
                                history.replace(`/categories/${data.data.id}/edit`)
                            } else {
                                history.push(`/categories/${data.data.id}/edit`)
                            }
                        } else {
                            history.push('/categories');
                        }
                    });
                })
                .catch((error) => {
                    snackbar.enqueueSnackbar('Erro ao salvar a categoria', {variant: "error"})
                    console.log(error)
                })
                .finally(() => setLoading(false));

    }

    return (
        <DefaultForm GridItemProps={{xs: 12, md: 6}} onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                inputRef={register}
                label="nome"
                fullWidth
                variant={"outlined"}
                disabled={loading}
                error={errors.name != undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
            />
            <TextField
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                disabled={loading}
                inputRef={register}
                InputLabelProps={{shrink: true}}
            />

            <FormControlLabel
                disabled={loading}
                control={
                    <Checkbox
                        name="is_active"
                        color="primary"
                        onChange={
                            () => setValue('is_active', !getValues()['is_active'])
                        }
                        checked={watch('is_active')}
                    />
                }
                label="Ativo?"
                labelPlacement="end"
            />
            <SubmitActions disableButtons={loading} handleSave={() =>
                    triggerValidation().then(isValid => {
                        isValid && onSubmit(getValues(), null)
                    })
                } />
        </DefaultForm>
    );
};