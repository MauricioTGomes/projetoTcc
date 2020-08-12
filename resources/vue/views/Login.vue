<template>
    <div id="login-container">
            <div class="block animation-fadeInQuick" style="height: 300px;">
                <div class="block-title">
                    <h2>Login</h2>
                </div>

                <form method="POST" accept-charset="UTF-8" class="form-horizontal" v-on:keyup.13="login">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <label >E-mail</label>
                            <input type="text" placeholder="Digite seu e-mail..." v-model="user.email"  class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" placeholder="Digite sua senha..." v-model="user.password" class="form-control"/>
                    </div>

                    <div class="form-group form-actions">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-success btn-fill float-right btn-sm pull-left" @click.prevent="login">
                                Entrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</template>
<script>

export default {
    name: 'Login',

    data () {
        return {
            user: {
                password: '',
                email: ''
            }
        }
    },
    methods: {
        login() {
            this.$http.post('auth/login', this.user).then(resp => {
                if (resp.data.error) {
                    this.$notifications.notify({title: "Atenção!", message: resp.data.mensagem, type: 'danger'})
                } else {
                    this.$store.dispatch('setUserToken', resp.data);
                    this.$http.defaults.headers.common['Authorization'] = "Bearer " + resp.data.access_token
                    return this.$router.push('/')
                }
            })
        }
    }
}

</script>
<style scoped>


#login-container {
    position: absolute;
    width: 300px;
    height: 300px;
    top: 30px;
    left: 50%;
    margin-left: -150px;
    z-index: 1000;
}

.block {
    -webkit-box-shadow: 0 2px 0 rgba(218, 229, 232, .5);
    box-shadow: 0 2px 0 rgba(218, 229, 232, .5);
}

.block {
    margin: 0 0 10px;
    padding: 20px 15px 1px;
    background-color: #ffffff;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
    -webkit-box-shadow: 0 2px 0 rgba(218, 224, 232, .5);
    box-shadow: 0 2px 0 rgba(218, 224, 232, .5);
}

.block-title {
    background: rgba(218, 229, 232, .15);
    margin: -20px -15px 20px;
    border-bottom: 2px solid #dae0e8;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
}

.block-title:hover {
    border-bottom-color: #dae5e8;
}

.block-title h1, .block-title h2, .block-title h3, .block-title h4, .block-title h5, .block-title h6 {
    display: inline-block;
    font-size: 14px;
    line-height: 1.4;
    margin: 0;
    padding: 10px 15px 9px;
    font-weight: 600;
    text-transform: uppercase;
}

body {
    overflow: hidden;
}
</style>
