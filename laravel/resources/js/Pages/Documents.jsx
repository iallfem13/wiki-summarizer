import AppLayout from '../Layouts/AppLayout';
import { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';

export default function Documents() {
    const [documents, setDocuments] = useState([]);
    const [selectedStatus, setSelectedStatus] = useState('completed');
    const [statuses, setStatuses] = useState([]);

    // retrieve the list of documents statuses and set them in state
    useEffect(() => {
        fetchStatuses();
    }, []);

    // fetch docs corresponding to the selected status when it changes
    useEffect(() => {
        fetchDocuments(selectedStatus);
    }, [selectedStatus]);

    // makes api call to get available statuses
    const fetchStatuses = async () => {
        try {
            const response = await axios.get('/api/document-statuses');
            // Map the raw status values into objects with `id` and `label`
            const formattedStatuses = response.data.map(status => ({
                id: status,
                label: status.charAt(0).toUpperCase() + status.slice(1),
            }));
            console.log(formattedStatuses);
            // Set a default selected status if none is provided
            setStatuses(formattedStatuses);
        } catch (error) {
            console.error('Failed to fetch document statuses:', error);
        }
    }

    // make api call to fetch list of documents based on selected status
    const fetchDocuments = async () => {
        try {
            console.log('selected status: ' + selectedStatus);
            const response = await axios.get('/api/documents', { params: { 'selectedStatus': selectedStatus }});
            console.log(response);
            setDocuments(response.data);
        } catch (error) {
            console.error('Failed to fetch documents:', error);
        }
    }

    // update the selected status based on dropdown selection
    const handleStatusChange = (event) => {
        setSelectedStatus(event.target.value);
    };

    return (
        <AppLayout>
            <h1 className="title">Summarized Documents</h1>

            {/* dropdown for document statuses */}
            <div className="mb-4 flex justify-end">
                <label htmlFor="status" className="text-sm font-medium text-gray-700 pr-2 pt-3">
                    Status
                </label>
                <select id="status" value={selectedStatus} onChange={handleStatusChange} className="mt-1 pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    {/* default option */}
                    {statuses.map((status) => (
                        <option key={status.id} value={status.id}>{status.label}</option>
                    ))}
                </select>
            </div>

            {/* document list */}
            <div className="mt-8">
                {documents.length > 0 ? (
                    // render each doc item
                    documents.map((doc) => (
                        <div key={doc.id} className="text-sm border-b p-2 flex justify-between items-start">
                            <div>
                                <Link className="text-[#486077] font-semibold" href={`/document/${doc.slug}`}>{doc.filename}</Link>
                            </div>
                            <div className="flex flex-col hover:text-gray-700 items-end text-[#a6b7c7]">
                                {doc.updated_at}
                            </div>
                        </div>
                    ))
                ) : (
                    <div className="bg-red-100 text-center py-4 rounded-lg dark:bg-red-900 dark:text-white">
                        No documents found for the selected status.
                    </div>
                )}
            </div>
        </AppLayout>
    )
}
