import * as React from 'react';
import {Container, makeStyles, Typography} from "@material-ui/core";

const useStyles = makeStyles({
    title: {
        color: "#999"
    }
});

type PageProps = {
    title: string
};
export const Page: React.FC<PageProps> = (props: PageProps) => {
    const classes = useStyles();
    return (
        <Container>
            <Typography className={classes.title} component={'h1'} variant={'h5'}>
                {props.title}
            </Typography>
        </Container>
    );
};