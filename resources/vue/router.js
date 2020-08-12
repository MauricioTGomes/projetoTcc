import DashboardLayout from "./layout/Dashboard";
import Login from "./views/Login";
import PessoaListar from "./views/Pessoa/Listar";
import PessoaForm from "./views/Pessoa/Form";

import ProdutoListar from "./views/Produto/Listar";
import ProdutoForm from "./views/Produto/Form";

const routes = [
    {
        path: '/logar',
        name: 'Logar',
        component: Login
    },
    {
        path: '/',
        component: DashboardLayout,
        name: 'Dashboard',
        children: [
            {
                path: 'pessoas/listar',
                name: 'PessoaListar',
                component: PessoaListar
            },
            {
                path: 'pessoas/adicionar',
                name: 'PessoaForm',
                component: PessoaForm
            },
            {
                path: 'produtos/listar',
                name: 'ProdutoListar',
                component: ProdutoListar
            },
            {
                path: 'produtos/adicionar',
                name: 'ProdutoForm',
                component: ProdutoForm
            },
        ]
    },
]

export default routes
