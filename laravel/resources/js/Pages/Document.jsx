import AppLayout from '../Layouts/AppLayout';

export default function Document({ document }) {
    return (
        <AppLayout>
            <h1 className="title">{document.filename}</h1>

            <div>
                <p className="whitespace-pre-wrap leading-relaxed">
                    {document.summary}
                </p>
                
                <div className="ml-10 mr-10 mt-6 mb-6 border-b border-gray-100 "></div>
                
                <p className="mt-3">
                    Tech stacks: 
                    {Array.isArray(document.tech_stacks) && document.tech_stacks.map((stack, index) => (
                        <span key={index} className="text-gray-800 bg-gray-100 px-3 py-1 rounded-full text-xs mr-2 font-medium">
                            #{stack.trim()}
                        </span>
                    ))}
                </p>
            </div>
        </AppLayout>
    );
}