import React, { useEffect, useRef } from "react";

const VisNetwork: React.FC = () => {
	// Create a ref to provide DOM access
	const visJsRef = useRef<HTMLDivElement>(null);
	useEffect(() => {
		// Once the ref is created, we'll be able to use vis
	}, [visJsRef]);
	return <div ref={visJsRef} />;
};

export default VisNetwork;