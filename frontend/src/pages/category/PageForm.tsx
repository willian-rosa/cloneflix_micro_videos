import * as React from 'react';
import {Page} from "../../components/Page";
import {Form} from "./Form";
import {useParams} from 'react-router'

const PageForm = () => {
    const params: {id?} = useParams();
    return (
        <Page title={params.id ? 'Editar Categoria' : 'Criar Categoria'}>
            <Form/>
        </Page>
    );
};

export default PageForm;