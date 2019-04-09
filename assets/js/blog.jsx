import React, {PureComponent} from 'react';
import ReactDOM from 'react-dom';
import renderHTML from 'react-render-html';

const postsPerPage = 5;

class Blog extends PureComponent {
    constructor(props) {
        super(props);
        this.state = {
            posts: '',
            numPosts: 0,
            page: 2,
            loadingArticles: false,
            finished: false,
        };
    }

    componentWillMount() {
        const numPosts = document.getElementById('num-posts').value;

        this.setState({
            ...this.state,
            numPosts: numPosts,
        });

        window.onscroll = () => {
            if (this.state.finished) {
                return;
            }

            if (document.documentElement.offsetHeight - (window.innerHeight + document.documentElement.scrollTop) < 300) {
                if (!this.state.loadingArticles) {
                    this.loadArticles();
                }
            }
        };
    }

    render() {
        return (<>
            {renderHTML(this.state.posts)}
            {
                this.state.loadingArticles ?
                    <div className="spinner">
                        <span className="oi oi-loop-circular"/>
                        <p>Carregant...</p>
                    </div>
                    : null
            }
        </>);
    }

    loadArticles() {
        this.setState({...this.state, loadingArticles: true});

        fetch("/api/blog?p=" + this.state.page)
            .then((response) => response.text())
            .then((posts) => {
                this.setState({
                    ...this.state,
                    posts: this.state.posts + posts,
                    page: this.state.page + 1,
                    loadingArticles: false,
                    finished: (this.state.page + 1) * postsPerPage >= this.state.numPosts,
                })
            });
    }
}

const element = <Blog/>;

ReactDOM.render(element, document.getElementById('blog'));
