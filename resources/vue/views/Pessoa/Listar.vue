<template>
    <div>
        <div class="row">
            <router-link :to="{name: 'PessoaForm'}" class="btn btn-success router-link-exact-active nav-item active">
                <i aria-hidden="true" class="fa fa-plus"></i>&nbsp; Adicionar
            </router-link>
        </div>
        <br>
        <div class="block">
            <table  class="display" style="width:100%" id="datatable">
                <thead>
                <tr>
                    <th width="50%">Razão Social / Nome</th>
                    <th width="20%">CPF / CNPJ</th>
                    <th width="20%">Cidade</th>
                    <th width="10%">Ações</th>
                </tr>
                </thead>

                <tbody>
                    <tr v-for="(model, indexItem) in arrayPessoas" :key="indexItem">
                        <td>{{ model.nome_completo }}</td>
                        <td>{{ model.documento_completo }}</td>
                        <td>{{ model.nome_cidade_completo }}</td>
                        <td>
                            <button class="btn btn-success router-link-exact-active nav-item active btn-xs" @click="$router.push({name: 'PessoaForm', params: {idPessoa: model.id}})" title="Alterar">
                                <i class="fa fa-pencil"></i>
                            </button>
                            
                            <button class="btn btn-danger router-link-exact-active nav-item active btn-xs" @click="deletar(model)" title="Excluir">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "Listar",
    data () {
        return {
            arrayPessoas: Array
        }
    },

    methods: {
        deletar(model) {
            const self = this
            self.$confirm({
                title: 'Eliminar: ' + model.nome_completo,
                message: 'Tem certeza que deseja realizar esta operação, não será possível reverter?',
                button: {
                    yes: 'Sim',
                    no: 'Não'
                },
                callback: confirm => {
                    if (confirm) {
                        self.$http.post(`/pessoas/deletar/${model.id}`).then(resp => {
                            if (resp.data.erro) {
                                self.$notifications.notify({title: "Atenção!", message: `${resp.data.mensagem}.`, type: 'danger'})
                            } else {
                                self.$notifications.notify({title: "Sucesso!", message: `${resp.data.mensagem}.`, type: 'success'})
                                self.$http.post('/pessoas/listar').then(resp => self.$set(self, 'arrayPessoas', resp.data))
                            }
                        })
                    }
                }
            })
        }
    },

    mounted() {
        this.$store.dispatch('setTitle', 'Pessoas') 

        this.$http.post('/pessoas/listar').then(resp => this.$set(this, 'arrayPessoas', resp.data))

        setTimeout(function() {
            $('#datatable').DataTable({
                "language": {
                    "lengthMenu": "_MENU_ por página",
                    "zeroRecords": "Nenhum registro encontrado...",
                    "info": "_PAGE_ de _PAGES_",
                    "infoEmpty": "Nenhum registro disponível",
                    "infoFiltered": "(_MAX_ total)",
                    "loadingRecords": "Processando...",
                    "processing":     "Processando...",
                    "search":         "Pesquisar:",
                    "paginate": {
                        "first":      "Primeira",
                        "last":       "Última",
                        "next":       "Próxima",
                        "previous":   "Anterior"
                    },
                }
            });
        }, 1000)
    }
}
</script>

