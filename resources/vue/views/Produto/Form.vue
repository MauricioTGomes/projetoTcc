<script>
    export default {
        data() {
            return {
                produto: {
                    ativo: {label: 'Sim', value: '1'},
                },
                editando: false
            }
        },
        methods: {
            async validaCampos (arrayDados) {
                let valido = true

                arrayDados.forEach(element => {
                    if (element.value === undefined || !element.value || element.value == '') {
                        valido = false
                        this.$notifications.notify({title: "Atenção!", message: `Campo: ${element.nome} não informado ou incorreto.`, type: 'danger'})
                    }
                });
                return valido
            },
            
            async validarDados () {
                let valido = await this.validaCampos([
                    {value: this.produto.nome, nome: 'Nome'},
                    {value: this.produto.apelido_produto, nome: 'Apelido'},
                    {value: this.produto.codigo, nome: 'Código'},
                    {value: this.produto.qtd_estoque, nome: 'Estoque'},
                ])

                if (valido) {
                    this.$http.post(`/produtos/${this.editando ? 'update' : 'gravar'}`, this.produto).then(resp => {
                        if (resp.data.erro) {
                            this.$notifications.notify({title: "Atenção!", message: `${resp.data.mensagem}.`, type: 'danger'})
                            return false
                        } else {
                            this.$notifications.notify({title: "Sucesso!", message: `${resp.data.mensagem}.`, type: 'success'})
                            return this.$router.push('/produtos/listar')
                        }
                    })
                }
            },

        },

        mounted() {
            const self = this

            self.$store.dispatch('setTitle', 'Adicionar produto')

            if (self.$route.params.idProduto != undefined) {
                self.$set(self, 'editando', true)

                self.$http.post(`/produtos/get/${self.$route.params.idProduto}`).then(resp => {
                    let produto = resp.data
                    produto.ativo = {value: produto.ativo, label: produto.ativo = '1' ? 'Sim' : 'Não'}
                    self.$set(self, 'produto', produto)
                })
            }
        }
    }

</script>

<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Ativa <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <v-select v-model="produto.ativo"
                                :options="[{value: 1, label: 'Sim'},{value: 0, label: 'Não'}]"></v-select>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Nome <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <input v-model="produto.nome" class="form-control" type="text" @keyup="$set(produto, 'apelido_produto', produto.nome)" placeholder="Nome produto"/>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Apelido <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <input v-model="produto.apelido_produto" class="form-control" type="text" placeholder="Apelido produto"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Código <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <input v-model="produto.codigo" class="form-control" type="number" placeholder="Código"/>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Estoque <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <input v-model="produto.qtd_estoque" class="form-control" v-money="produto.qtd_estoque" placeholder="Quantidade estoque"/>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Valor venda (R$) <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <input v-model="produto.valor_venda" v-money="produto.valor_venda" class="form-control" placeholder="Valor venda (R$)"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group form-actions">
                    <button type="button" @click="validarDados" class="btn btn-success">Salvar</button>
                    <a href="/produtos/listar" class="btn btn-effect-ripple btn-danger">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</template>