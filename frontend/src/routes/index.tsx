import {RouteProps} from 'react-router-dom';
import Dashboard from "../pages/Dashboard";

import CategoryList from "../pages/category/PageList";
import CategoryPageForm from "../pages/category/PageForm";

import GenreList from "../pages/genre/PageList";
import GenrePageForm from "../pages/genre/PageForm";

import CastMembersList from "../pages/cast-members/PageList";
import CastMembersPageForm from "../pages/cast-members/PageForm";


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
        component: CategoryPageForm,
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
    {
        name: 'genres.create',
        label: 'Criar Gêneros',
        path: '/genres/create',
        component: GenrePageForm,
        exact: true,
    },
    {
        name: 'genres.edit',
        label: 'Editar Gênero',
        path: '/genres/:id/edit',
        component: GenrePageForm,
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
    {
        name: 'cast_members.crate',
        label: 'Criar membro de elenco',
        path: '/cast-members/create',
        component: CastMembersPageForm,
        exact: true,
    },
    {
        name: 'cast_members.edit',
        label: 'Editar membro de elenco',
        path: '/cast-members/:id/edit',
        component: CastMembersPageForm,
        exact: true,
    },
];

export default routes;