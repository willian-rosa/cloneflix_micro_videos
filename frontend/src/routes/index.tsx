import {RouteProps} from 'react-router-dom';
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/PageList";
import GenreList from "../pages/genre/PageList";
import CastMembersList from "../pages/cast-members/PageList";


export interface MyRouteProps extends RouteProps {
    name: string;
    label: string;
}

const  routes: MyRouteProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true,
    },

    // ################ Categories
    {
        name: 'categories.list',
        label: 'Listar Categorias',
        path: '/categories',
        component: CategoryList,
        exact: true,
    },
    {
        name: 'categories.create',
        label: 'Criar Categoria',
        path: '/categories/create',
        component: CategoryList,
        exact: true,
    },
    {
        name: 'categories.edit',
        label: 'Editar Categoria',
        path: '/categories/:id/edit',
        component: CategoryList,
        exact: true,
    },
    // ################ Genres
    {
        name: 'genres.list',
        label: 'Listar Gêneros',
        path: '/genres',
        component: GenreList,
        exact: true,
    },
    // ################ Cast Members
    {
        name: 'cast_members.list',
        label: 'Listar membros de elencos',
        path: '/cast-members',
        component: CastMembersList,
        exact: true,
    },
];

export default routes;