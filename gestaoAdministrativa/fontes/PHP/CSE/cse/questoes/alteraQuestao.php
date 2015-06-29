<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Arquivo de instância para Questões
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.94
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");

    if (!(isset($ctrl))) {
        $ctrl = 0;
        unset($sessao->transf);
        unset($sessao->transf2);
    }

    switch ($ctrl) {
        case 0:
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                    cod_questao,
                    nom_questao,
                    valor_padrao,
                    tipo
                    FROM
                    cse.questao_censo";
        //echo $select."<br>";
        if (!(isset($sessao->transf2['questao']))) {
            $sessao->transf['questao'] = "";
            $sessao->transf['questao'] = $select;
            $sessao->transf2['questao'] = "cod_questao";
        }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf['questao'],"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(".$sessao->transf2['questao'].")","ASC");
        $sSQL = $paginacao->geraSQL();
        //echo $sSQL."<br>";
        $dbConfig->abreSelecao($sSQL);
        while (!$dbConfig->eof()) {
            $lista[] = $dbConfig->pegaCampo("cod_questao")."/".$dbConfig->pegaCampo("nom_questao")."/".
            $dbConfig->pegaCampo("valor_padrao")."/".$dbConfig->pegaCampo("tipo");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
<table width="100%">
    <tr>
        <td colspan="5" class="alt_dados">
            Questões Cadastradas
        </td>
    <tr>
        <td class="label" width="5%">
            &nbsp;
        </td>
        <td class="labelcenter" width="12%">
            Código
        </td>
        <td class="labelcenter" width="30%">
            Questão
        </td>
        <td class="labelcenter" width="50%">
            Valor Padrão
        </td>
        <td class="label">
            &nbsp;
        </td>
    </tr>
<?php
    $iCont = $paginacao->contador();
    if ($lista != "") {
        while (list ($cod, $val) = each ($lista)) { //mostra os tipos de processos na tela
                $fim = explode("/", $val);
?>
    <tr>
        <td class="label">
            <?=$iCont++?>&nbsp;
        </td>
        <td class="show_dados_right">
            <?=$fim[0];?>&nbsp;
        </td>
        <td class="show_dados">
            <?=$fim[1];?>&nbsp;
        </td>
        <td class="show_dados">
<?php
    if ($fim[3] == "l" or $fim[3] == "m") {
        $arTipo = explode("\n", $fim[2] );
        echo "            ".$arTipo[0]."...";
    } else {
        echo $fim[2];
    }
?>&nbsp;
        </td>
        <td class="botao">
            <a href="alteraQuestao.php?<?=$sessao->id;?>&ctrl=1&codQuestao=<?=$fim[0];?>&pagina=<?=$pagina;?>">
            <img src="<?=CAM_FW_IMAGENS."btneditar.gif";?>" border="0">
            </a>
        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class="show_dados_center" colspan="5">
            <b>Nenhum registro encontrado!</b>
        </td>
    </tr>
<?php
    }
?>
</table>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
            <?=$paginacao->mostraLinks();?>
            </font>
        </td>
    </tr>
</table>
<?php
    break;
        case 1:
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT *
                    FROM
                    cse.questao_censo
                    WHERE
                    cod_questao = ".$codQuestao;
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        $var = array(
        codQuestao=>$dbConfig->pegaCampo("cod_questao"),
        anoExercicio=>$dbConfig->pegaCampo("exercicio"),
        nomQuestao=>$dbConfig->pegaCampo("nom_questao"),
        ordemQuestao=>$dbConfig->pegaCampo("ordem"),
        tipo=>$dbConfig->pegaCampo("tipo"),
        valorPadrao=>$dbConfig->pegaCampo("valor_padrao")
        );
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomQuestao.value;
            if (campo == "") {
            mensagem += "@Campo Questão inválido!()";
            erro = true;
        }

        campo = document.frm.ordemQuestao.value;
            if (campo == "") {
            mensagem += "@Campo Ordem inválido!()";
            erro = true;
        }

        campo = document.frm.ordemQuestao.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Ordem inválido!("+campo+")";
            erro = true;
        }

        campo = document.frm.tipo.value;
            if (campo == "xxx") {
            mensagem += "@Campo Tipo inválido!()";
            erro = true;
        }

        campo = document.frm.anoExercicio.value;
            if (campo == "") {
            mensagem += "@Campo Exercício inválido!()";
            erro = true;
        }

        campo = document.frm.anoExercicio.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Exercício inválido!("+campo+")";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>','');
                return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }

      function Cancela()
      {
        document.frm.ctrl.value=0;
        document.frm.submit();
      }
</script>
<form action="alteraQuestao.php?<?=$sessao->id?>" method="POST" name="frm">
<table width="100%">
    <input type="hidden" name="codQuestao" value="<?=$var[codQuestao]?>">
    <input type="hidden" name="ctrl" value="2">
    <input type="hidden" name="pagina" value="<?=$pagina;?>">
    <tr>
        <td class="alt_dados" colspan="2">
            Dados da Questão
        </td>
    </tr>
    <tr>
        <td class="label" title="Descrição da questão" width="20%">
            *Questão
        </td>
        <td class="field"  width="80%">
            <input type="text" name="nomQuestao" value="<?=$var[nomQuestao]?>" size="60" maxlength="160">
        </td>
    </tr>
    <tr>
        <td class="label" title="Ordem da questão">
            *Ordem
        </td>
        <td class="field">
            <input type="text" name="ordemQuestao" value="<?=$var[ordemQuestao]?>" size="4" maxlength="4">
        </td>
    </tr>
    <tr>
        <td class="label" title="Ano exercício da questão">
            *Exercício
        </td>
        <td class="field">
            <input type="text" name="anoExercicio" value="<?=$var[anoExercicio]?>" size="4" maxlength="4">
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo da questão">
            *Tipo
        </td>
        <td class="field">
            <select name="tipo">
                <option value=xxx>Selecione</option>
<?php if ($var[tipo] == 't') { ?>
                <option value="t" selected>Texto</option>
<?php } else { ?>
                <option value="t">Texto</option>
<?php }if ($var[tipo] == 'n') { ?>
                <option value="n" selected>Número</option>
<?php } else { ?>
                <option value="n">Número</option>
<?php }if ($var[tipo] == 'l') { ?>
                <option value="l" selected>Lista</option>
<?php } else { ?>
                <option value="l">Lista</option>
<?php }if ($var[tipo] == "m") { ?>
                <option value="m" selected>Lista Múltipla</option>
<?php } else {?>
                <option value="m">Lista Múltipla</option>
<?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Valores padrão para as respostas">
            Valor Padrão
        </td>
        <td class="field">
            <textarea name="valorPadrao" cols="30" rows="6"><?=$var[valorPadrao];?></textarea>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <?php geraBotaoAltera(); ?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
        case 2:
            $var = array(
            codQuestao=>$codQuestao,
            anoExercicio=>$anoExercicio,
            nomQuestao=>$nomQuestao,
            ordemQuestao=>$ordemQuestao,
            tipo=>$tipo,
            valorPadrao=>$valorPadrao
            );
            $alterar = new cse;
            if ($alterar->alteraQuestao($var)) {
                include(CAM_FW_LEGADO."auditoriaLegada.class.php");
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $nomQuestao);
                $audicao->insereAuditoria();
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomQuestao.'","alterar","aviso","'.$sessao->id.'","");
                    window.location = "alteraQuestao.php?'.$sessao->id.'&pagina='.$pagina.'";
                    </script>';
            } else {
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomQuestao.'","n_alterar","erro","'.$sessao->id.'&pagina='.$pagina.'","");
                    window.location = "alteraQuestao.php?'.$sessao->id.'";
                    </script>';
            }
    break;
    }
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
