import * as React from 'react';
import {useEffect, useState} from 'react';
import {
    Box,
    Button,
    ButtonProps,
    FormControl,
    FormControlLabel,
    FormHelperText,
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

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string().label('Nome').required(),
    type: yup.boolean().label('Tipo').required()
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
                            history.replace(`/cast-members/${response.data.data.id}/edit`)
                        } else {
                            history.push(`/cast-members/${response.data.data.id}/edit`)
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

    let typeWatch = watch('type') + "";

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

            <FormControl margin="normal" disabled={loading} error={errors.type !== undefined}>
                <FormLabel component="legend">Tipo</FormLabel>
                <RadioGroup
                    name="type"
                    aria-label="Tipo"
                    onChange={(e) => {
                        setValue('type', parseInt(e.target.value));
                        setValue('name', parseInt(e.target.value));
                        console.log(parseInt(e.target.value), watch('type'), typeWatch)
                    }}
                    value={typeWatch}
                >
                    <FormControlLabel value="1" control={<Radio color="primary" />} label="Diretor" />
                    <FormControlLabel value="2" control={<Radio color="primary" />} label="Ator" />
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