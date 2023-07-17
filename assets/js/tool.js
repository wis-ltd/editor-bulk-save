class Tool {
	enable() {
		document.querySelector('.js--start-bulk-update').addEventListener('submit', function (e) {
			e.preventDefault();

			const response = fetch(`${window.ajaxurl}?action=ebs_run`);
			response.then((response) => {
				console.log(response.json());
			});
		});
	}
}

const tool = new Tool();
tool.enable();
export default Tool;
