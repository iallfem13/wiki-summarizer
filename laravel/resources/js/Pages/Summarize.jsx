import AppLayout from '../Layouts/AppLayout';
import { useState } from 'react';
import axios from 'axios';

export default function Summarize() {
    const [title, setTitle] = useState('');
    const [text, setText] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);

    // event handlers for input changes
    // ensure inputs are controller components (the value is managed by React state)
    const handleTitleChange = (e) => {
        setTitle(e.target.value);
        setError(null);
        setSuccess(null);
    }
    const handleTextChange = (e) => {
        setText(e.target.value);
        setError(null);
        setSuccess(null);
    }

    // form submission handler
    const handleSubmit = async (e) => {
        e.preventDefault();

        // reset messages
        setError(null);
        setSuccess(null);

        // validate fields are not empty
        if (!title || !text) {
            setError('Please fill in all fields');
            return;
        }

        setIsLoading(true);

        try {
            const response = await axios.post('api/submit', {title, text});
            //setTitle('');
            //setText('');
            setSuccess(response.data.message || 'Text submitted for summarization');
        } catch (err) {
            if (err.response && err.response.data.error) {
                setError(err.response.data.error);
            } else {
                setError('An error has ocurred while submitting the text');
            }
        } finally {
            setIsLoading(false);
        }
    }

    return (
        <AppLayout>
            <h1 className="title">Summarize text</h1>
            <div className="alerts">
                { error && 
                    <p className="alert-error">{error}</p> 
                }
                { success && 
                    <p className="alert-success">{success}</p> 
                }
            </div>

            <div>
                <form onSubmit={handleSubmit} className="flex flex-col w-[70%] items-stretch  space-y-1">
                    <label className="form-label">Title</label>
                    <input className="form-input"
                        type="text" value={title} onChange={handleTitleChange} disabled={isLoading} />

                    
                    <label className="form-label">Text</label>
                    <textarea className="form-textarea"
                        value={text} onChange={handleTextChange} disabled={isLoading} />
                    
                    <div className="flex justify-end">
                        <button className="btn-primary mt-4 py-3 px-6" disabled={isLoading || !title || !text}>
                            {isLoading ? 'Submitting...' : 'Send'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    )
}
