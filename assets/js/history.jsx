import React, {PureComponent} from 'react';
import ReactDOM from 'react-dom';
import Fuse from 'fuse.js';
import '../scss/history.scss';

class Caminades extends PureComponent {
    constructor(props) {
        super(props);
        this.state = {
            search: {
                text: '',
                results: [],
            },
            caminades: null,
        };
    }

    componentDidMount() {
        fetch("/api/history")
            .then((response) => response.json())
            .then((caminades) => {
                this.fuse = new Fuse(caminades, {
                    shouldSort: true,
                    threshold: 0.4,
                    minMatchCharLength: 3,
                    keys: ['path']
                });
                this.setState({
                    caminades: caminades,
                    search: {
                        text: '',
                        results: caminades,
                    },
                })
            });
    }

    filter(text) {
        this.setState({
            search: {
                text: text,
                results: text === '' ? this.state.caminades : this.fuse.search(text),
            }
        });
    }

    render() {
        if (this.state.caminades === null) {
            return <div className="spinner">
                <span className="glyphicon glyphicon-refresh"/>
                <p>Carregant...</p>
            </div>;
        }

        return (
            <>
                <form className="form-inline search" onSubmit={e => {e.preventDefault(); return false;}}>
                    <div className="form-group">
                    <label className="sr-only" htmlFor="search">Buscar per lloc</label>
                    <input
                        type="text"
                        id="search"
                        className="form-control"
                        value={this.state.search.text}
                        onChange={e => this.filter(e.target.value)}
                        placeholder="Buscar per lloc"
                    />
                    </div>
                </form>
                <div className="caminades">
                    {this.state.search.results.map((caminada) => (
                        <div key={caminada.id} className="caminada">
                            <a href={"/caminades/" + caminada.number}>
                                <div className="panel panel-success">
                                    <div className="panel-heading">
                                        <h3 className="panel-title">
                                            {caminada.number}a Caminada ({caminada.year})
                                        </h3>
                                    </div>
                                    <div className="panel-body">
                                        {caminada.path.join(' - ')}
                                    </div>
                                </div>
                            </a>
                        </div>
                    ))}
                </div>
            </>
        );
    }
}

const element = <Caminades/>;

ReactDOM.render(element, document.getElementById('history'));
