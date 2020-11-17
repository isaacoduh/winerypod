<style scoped>
.action-link{
    cursor: pointer;
}
</style>

<template>
    <div>
        <div v-if="tokens.length > 0">
            <div class="card card-default">
                <div class="card-header">Authorized Applications</div>
                <div class="card-body">
                    <table class="table table-striped mb-1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Scopes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="token in tokens">
                                <td style="veritical-align: middle;">
                                    {{token.client.name}}
                                </td>
                                <td style="veritical-align: middle;">
                                    <span v-if="token.scope.length > 0">
                                        {{token.scopes.join(', ')}}
                                    </span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <a class="action-link text-danger" @click="revoke(token)" >Revoke</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    /**
     * Component data
     */
    data(){
        return {
            tokens: []
        };
    },

    mounted(){
        this.prepareComponent();
    },
    methods: {
        prepareComponent(){
            this.getTokens();
        },

        /***
         * Get all authorized tokens for the user
         */
        getTokens(){
            axios.get('/oauth/tokens').then(response => {
                this.tokens = response.data;
            });
        },

        /**
         * Revoke the given token
         */
        revoke(token){
            axios.delete('/oauth/tokens/'+token.id).then(response => {
                this.getTokens();
            });
        }
    }
}
</script>
