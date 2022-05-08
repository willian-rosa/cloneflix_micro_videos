import * as React from 'react';
import {
    Box,
    Button,
    ButtonProps,
    FormControl,
    FormControlLabel, FormHelperText,
    FormLabel,
    Radio,
    RadioGroup,
    TextField
} from "@material-ui/core";
import {useForm} from "react-hook-form";
import {makeStyles, Theme} from "@material-ui/core/styles";
import castMemberHttp from "../../util/http/cast-member-http";
import * as yup from "../../util/vendor/yup";
import {useSnackbar} from "notistack";
import {useHistory, useParams} from "react-router";
import {useEffect, useState} from "react";
import categoryHttp from "../../util/http/category-http";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string().label('Nome').required(),
    type: yup.boolean().label('Nome').required()
});


export const Form = () => {
    const snackbar = useSnackbar();
    const history = useHistory();
    const classes = useStyles();

    const {register, handleSubmit, getValues, errors, reset, watch, setValue} = useForm({
        validationSchema,

    });
    const params: {id?} = useParams();
    const [loading, setLoading] = useState<boolean>(false);
    const [castMember, setCastMember] = useState<{id: string} | null>(null);

    const propsButton: ButtonProps = {
        variant: "contained",
        color: "secondary",
        className: classes.submit,
        disabled: loading
    };

    useEffect(() => {
        if (!params.id) {
            return;
        }
        setLoading(true);
        castMemberHttp
            .get(params.id)
            .then(({data}) => {
                setCastMember(data.data);
                reset(data.data);
            })
            .catch((error) => {
                snackbar.enqueueSnackbar('Erro ao buscar Membro da Equipe', {variant: "error"})
                console.log(error);
            })
            .finally(() => setLoading(false))
    }, []);

    function onSubmit(formData, event) {
        setLoading(true);

        const http = !castMember
            ? castMemberHttp.create(formData)
            : castMemberHttp.update(castMember.id, formData)


        http.then((response) => {
                snackbar.enqueueSnackbar('Membro elenco salvo com sucesso', {variant: "success"})
                setTimeout(() => {
                    if (event) {
                        if (params.id) {
                            history.replace(`/cast-members/${response.data.id}/edit`)
                        } else {
                            history.push(`/cast-members/${response.data.id}/edit`)
                        }
                    } else {
                        history.push('/cast-members');
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
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                inputRef={register}
                label="Nome"
                fullWidth
                variant={"outlined"}
                disabled={loading}
                error={errors.name != undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
            />

            <FormControl component="fieldset" margin="normal" disabled={loading}>
                <FormLabel component="legend">Tipo</FormLabel>
                <RadioGroup aria-label="Tipo">
                    <FormControlLabel name="type" control={<Radio color="primary" />} label="Diretor" value="1" inputRef={register}/>
                    <FormControlLabel name="type" control={<Radio color="primary" />} label="Ator" value="2" inputRef={register}/>
                </RadioGroup>
                <FormHelperText error={errors.type != undefined} >{errors.type && errors.type.message}</FormHelperText>
            </FormControl>

            <Box dir="rtl">
                <Button {...propsButton} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button {...propsButton} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};