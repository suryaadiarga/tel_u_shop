import { HTMLAttributes } from 'react';

// Appearance dropdown removed; keep placeholder to avoid breaking imports.
export default function AppearanceToggleDropdown({
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement>) {
    return (
        <div className={className} {...props}>
            <div className="h-9 w-9 rounded-md flex items-center justify-center">L</div>
        </div>
    );
}
