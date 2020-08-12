<script>
    export default {
        data() {
            return {
                pessoa: {
                    cliente: true,
                    fornecedor: false,
                    ativo: {label: 'Sim', value: '1'},
                    tipo: {label: 'Física', value: 'FISICO'},
                    nome: '',
                    razao_social: '',
                    fantasia: '',
                    cnpj: '',
                    cpf: '',
                },
                cidadesSelect: [],
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
                let camposValidar = [
                    {value: this.pessoa.endereco, nome: 'Endereço'},
                    {value: this.pessoa.endereco_nro, nome: 'Número'},
                    {value: this.pessoa.cep, nome: 'CEP'},
                    {value: this.pessoa.cidade_id, nome: 'Cidade'},
                    {value: this.pessoa.fone, nome: 'Telefone'}
                ]

                if (this.pessoa.tipo.value == 'FISICO') {
                    camposValidar.push({value: this.pessoa.nome, nome: 'Nome'})
                    camposValidar.push({value: this.pessoa.cpf, nome: 'CPF'})
                } else {
                    camposValidar.push({value: this.pessoa.razao_social, nome: 'Razão social'})
                    camposValidar.push({value: this.pessoa.fantasia, nome: 'Fantasia'})
                    camposValidar.push({value: this.pessoa.cnpj, nome: 'CNPJ'})
                }


                let valido = await this.validaCampos(camposValidar)

                if (valido) {
                    this.$http.post(`/pessoas/${this.editando ? 'update' : 'gravar'}`, this.pessoa).then(resp => {
                        if (resp.data.erro) {
                            this.$notifications.notify({title: "Atenção!", message: `${resp.data.mensagem}.`, type: 'danger'})
                            return false
                        } else {
                            this.$notifications.notify({title: "Sucesso!", message: `${resp.data.mensagem}.`, type: 'success'})
                            return this.$router.push('/pessoas/listar')
                        }
                    })
                }
            },

        },

        mounted() {
            const self = this

            self.$store.dispatch('setTitle', 'Adicionar pessoa')
            self.$http.post('/getCidadesSelect').then(resp => self.$set(self, 'cidadesSelect', resp.data))

            if (self.$route.params.idPessoa != undefined) {
                self.$set(self, 'editando', true)

                self.$http.post(`/pessoas/get/${self.$route.params.idPessoa}`).then(resp => {
                    let pessoa = resp.data

                    pessoa.ativo = {value: pessoa.ativo, label: pessoa.ativo = '1' ? 'Sim' : 'Não'}
                    pessoa.tipo = {value: pessoa.tipo, label: pessoa.tipo = 'FISICO' ? 'Física' : 'Jurídica'}
                    pessoa.cidade = self.cidadesSelect.filter(cid => cid.value == pessoa.cidade_id)[0]
                    pessoa.fornecedor = pessoa.fornecedor == '1'
                    pessoa.cliente = pessoa.cliente == '1'
                    self.$set(self, 'pessoa', pessoa)
                })
            }
        }
    }

</script>

<template>
    <div>
        <div class="row">
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <label class="csscheckbox csscheckbox-danger">
                            <input v-model="pessoa.cliente" name="cliente" type="checkbox">
                            <span></span>&nbsp;&nbsp;Cliente
                        </label>
                    </div>

                    <div class="col-sm-6 col-xs-6">
                        <label class="csscheckbox csscheckbox-danger">
                            <input v-model="pessoa.fornecedor" name="fornecedor"
                                   type="checkbox"> <span></span>&nbsp;&nbsp;Fornecedor
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Tipo <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <v-select v-model="pessoa.tipo" name="tipo"
                                :options="[{value: 'FISICO', label: 'Física'},{value: 'JURIDICO', label: 'Jurídica'}]"></v-select>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Ativa <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <v-select v-model="pessoa.ativo" name="ativo"
                                :options="[{value: 1, label: 'Sim'},{value: 0, label: 'Não'}]"></v-select>
                </div>
            </div>
        </div>

        <div v-show="pessoa.tipo.value == 'FISICO'">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Nome <i class="text-danger" title="Campo obrigatório">*</i></label>
                        <input type="text" v-model="pessoa.nome" name="nome" class="form-control">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>CPF<i class="text-danger" title="Campo obrigatório">*</i></label>
                        <the-mask type="text" class="form-control" :mask="'###.###.###-###'" :masked="true"
                              v-model="pessoa.cpf"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>RG</label>
                        <input type="text" v-model="pessoa.rg" name="rg" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-show="pessoa.tipo.value == 'JURIDICO'">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Razão social <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <input type="text" v-model="pessoa.razao_social" name="razao_social" class="form-control">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Fantasia <i class="text-danger" title="campo obrigatório">*</i></label>
                    <input type="text" v-model="pessoa.fantasia" name="fantasia" class="form-control" id="fantasia">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>CNPJ <i class="text-danger" title="campo obrigatório">*</i></label>
                    <the-mask type="text" class="form-control" :mask="'##.###.###/####-##'" :masked="true"
                              v-model="pessoa.cnpj"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4" v-if="pessoa.tipo.value == 'JURIDICO'">
                <div class="form-group">
                    <label>Inscrição estadual</label>
                    <input type="text" v-model="pessoa.ie" name="ie" class="form-control">
                </div>
            </div>
        
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Telefone <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <the-mask type="text" class="form-control" :mask="['(##) #####-####', '(##) ####-####']"
                              v-model="pessoa.fone" name="fone"/>
                </div>
            </div>
        
            <div class="col-sm-4">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="text" v-model="pessoa.email" name="email" class="form-control" id="email">
                </div>
            </div>
        </div>

        <h3>Endereço</h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>CEP <i class="text-danger" title="campo obrigatório">*</i></label>
                    <the-mask type="text" class="form-control" :mask="'##.###-###'"
                              v-model="pessoa.cep"/>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Endereço <i class="text-danger" title="campo obrigatório">*</i></label>
                    <input type="text" v-model="pessoa.endereco" name="endereco" class="form-control">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Número <i class="text-danger" title="campo obrigatório">*</i></label>
                    <input type="text" v-model="pessoa.endereco_nro" name="endereco_nro" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Cidade <i class="text-danger" title="Campo obrigatório">*</i></label>
                    <v-select v-model="pessoa.cidade" @input="(cidade) => $set(pessoa, 'cidade_id', cidade.value == undefined ? null : cidade.value)" :options="cidadesSelect"></v-select>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>Complemento</label>
                    <input type="text" v-model="pessoa.complemento" name="complemento" class="form-control">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Bairro <i class="text-danger" title="campo obrigatório">*</i></label>
                    <input type="text" v-model="pessoa.bairro" name="bairro" class="form-control" id="bairro">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group form-actions">
                    <button type="button" @click="validarDados" class="btn btn-success">Salvar</button>
                    <a href="/pessoas/listar" class="btn btn-effect-ripple btn-danger">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</template>