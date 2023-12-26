<?php
require("./assets/popup.php");
$p1 = new PopUp(
    title: "Hello, how are ya?",
    submit_text: "Ok",
    submit_url: "Doesn't works, idk why 0_0",
    allowclose: true,
    close_text: "Naaa",
    close_redirecturl:null,
    description: null,
    method: "post",
    type: "input_1", //can be alert or input_1 (alert isn't that usefull)
    img: "https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/259bab17-3152-48d3-8cbf-10a1cb138953/dg600di-0738ac51-66e9-4e7f-bc8f-da8e8d2e3e94.jpg/v1/fill/w_1192,h_670,q_70,strp/the_gateway_by_pawkadigital_dg600di-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9NDMyMCIsInBhdGgiOiJcL2ZcLzI1OWJhYjE3LTMxNTItNDhkMy04Y2JmLTEwYTFjYjEzODk1M1wvZGc2MDBkaS0wNzM4YWM1MS02NmU5LTRlN2YtYmM4Zi1kYThlOGQyZTNlOTQuanBnIiwid2lkdGgiOiI8PTc2ODAifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.FfgvhZk-ez100NLtD3wjdrEa8GUiCdLagOAE6nS6Z_I");
//https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/cf156c54-eec4-4624-9010-a3044aa71570/dg8se8d-801aae58-7a6b-4fd6-ad0e-23160306468f.jpg/v1/fill/w_1194,h_669,q_70,strp/rainbow_northern_lights_by_sdwrage_dg8se8d-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9NzE4IiwicGF0aCI6IlwvZlwvY2YxNTZjNTQtZWVjNC00NjI0LTkwMTAtYTMwNDRhYTcxNTcwXC9kZzhzZThkLTgwMWFhZTU4LTdhNmItNGZkNi1hZDBlLTIzMTYwMzA2NDY4Zi5qcGciLCJ3aWR0aCI6Ijw9MTI4MCJ9XV0sImF1ZCI6WyJ1cm46c2VydmljZTppbWFnZS5vcGVyYXRpb25zIl19.ZJYJ4p8E86lgKsauIL2wXhlpHGnR_n_dO4t077e4FIg
//https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/8e1d5f84-6bdf-4c70-99a5-1716cfe4992c/dcrsjuz-bcf8d4bc-ed8c-4652-aaad-8149319ee156.jpg/v1/fill/w_1378,h_580,q_70,strp/research_outpost_by_andisreinbergs_dcrsjuz-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9MTgyNCIsInBhdGgiOiJcL2ZcLzhlMWQ1Zjg0LTZiZGYtNGM3MC05OWE1LTE3MTZjZmU0OTkyY1wvZGNyc2p1ei1iY2Y4ZDRiYy1lZDhjLTQ2NTItYWFhZC04MTQ5MzE5ZWUxNTYuanBnIiwid2lkdGgiOiI8PTQzMzIifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.2sF9MU6R6-XByHsUE0cUuWR67cpPw5ClR0frc84-8Cw
$p1->addInput(new Input(id: "u", type: "text", title: "Username"));
$p1->addInput(new Input(id: "e", type: "email", title: "E-Mail"));
$p1->addInput(new Input(id: "p", type: "password", title: "Password"));
$p1->addInput(new Input(id: "d", type: "dropdown", title: "List 1 (2+ items, 1 item will crash site)", 
                        json_content_encoded: '{"OP1":"Option 1", "OP2":"Option 2"}', selected: "OP2"));
$p1->addInput(new Input(id: "s", type: "select", title: "List 2", 
                        json_content_encoded: '{"OP1":"Option 1", "OP2":"Option 2"}', selected: "OP2"));
$p1->addInput(new Input(id: "l", type: "link", title: "YEAAA", placeholder: "https://deviantart.com"));

if($p1->generateCode()){}
echo($p1->getCode());
echo('<script>'.$p1->script_js().'</script>');

?>
