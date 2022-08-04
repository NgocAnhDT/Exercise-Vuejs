<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-2">
                <div class="card">
                    <div class="card-header">Post</div>
                    <div class="card-body">
                        <div class="row">
                            <form @submit.prevent="savePost">
                                <div class="form-group">
                                    <textarea name="content" v-model="content" class="col-12"
                                        placeholder="What's on your mind?">
                                    </textarea>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-sm btn-primary float-end">Post</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">List posts</div>
                    <div class="card-body" v-for="post in posts" :key="post.id">
                        <b>{{ post.user.name }}</b>
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <p>{{ post.content }}</p>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-secondary" id="btn-relpy"
                                    data-bs-toggle="collapse" data-bs-target="#comment"
                                    @click="showComment(post.id)">Reply</button>
                            </div>
                        </div>
                        <div id="comment" class="collapse">
                            <input name="comment" v-model="comment" v-show="isReply == post.id" class="col-12 mb-2"
                                placeholder="Reply..." @keyup.enter="submitComment(post.id)">
                        </div>
                        <div class="card p-2 mb-1" v-for="comment in post.comments" :key="comment.id">
                            <p></p>
                            <p class="small mb-0">{{ comment.comment }}</p>
                            <a href="#sub-comment" data-bs-toggle="collapse" role="button" id="btn-relpy"
                                class="small mb-0" @click="showComment(comment.id)">Reply</a>
                            <div id="sub-comment" class="collapse">
                                <input name="sub_comment" v-model="sub_comment" class="col-12 small"
                                    v-show="isReply == comment.id" placeholder="Reply..."
                                    @keyup.enter="submitSubComment(comment.id)">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import toastr from '../../../node_modules/toastr/build/toastr.min.js';
    import VueLaroute from 'vue-laroute';
    import routes from '../../../public/js/laroute.js'

    Vue.use(VueLaroute, {
        routes,
        accessor: '$routes', // Optional: the global variable for accessing the router
    });

    export default {
        data() {
            return {
                content: '',
                posts: [],
                comment: '',
                isReply: '',
                sub_comment: '',
            }
        },
        created() {
            axios.get(this.$routes.route('post.index')).then(response => {
                this.posts = response.data;
            })
        },
        methods: {
            savePost() {
                axios.post(this.$routes.route('post.store'), {
                    content: this.content
                }, {headers:{"Content-Type" : "application/json"}})
                .then(response => {
                    this.posts.unshift(response.data.post);
                    toastr.success(
                        response.data.success,
                        '',
                        {
                            timeOut: 2000
                        });
                    this.content = '';
                })
                .catch(error => {
                    toastr.error(
                        error,
                        '',
                        {
                            timeOut: 2000
                        });
                })
            },
            showComment(el) {
                this.isReply = el;
                this.$forceUpdate();
            },
            submitComment(id) {
                axios.post(this.$routes.route('comment'), {
                    comment: this.comment,
                    post_id: id
                }, {headers:{"Content-Type" : "application/json"}})
                .then(response => {
                    this.posts.forEach(post => {
                        if (post.id == id) {
                            post.comments.unshift(response.data);
                        }
                    })
                    this.comment = '';
                    this.isReply = '';
                })
                .catch()
            },
        },
        
    }
</script>
