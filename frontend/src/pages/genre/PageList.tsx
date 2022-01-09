import * as React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import {Link} from "react-router-dom";
import AddIcon from "@material-ui/icons/Add"
import Table from "./Table";

const List = () => {
    return (
        <Page title="Listagem Gênero">
            <Box dir={'rtl'} paddingBottom={2}>
                <Fab
                    color="secondary"
                    title="Adicionar Gênero"
                    size="small"
                    component={Link}
                    to="/genres/create"
                >
                    <AddIcon/>
                </Fab>
            </Box>
            <Box>
                <Table/>
            </Box>
        </Page>
    );
};

export default List;