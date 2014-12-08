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
    * Arquivo que seleciona o método de consulta
    * Data de Criação   : 28/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira
    * @author Desenvolvedor Marcelo Boezzio Paulino

    * @ignore

    $Revision: 22089 $
    $Name$
    $Autor: $
    $Date: 2007-04-23 17:57:19 -0300 (Seg, 23 Abr 2007) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.33  2007/04/23 20:57:19  rodrigo_sr
Bug #8351#

Revision 1.32  2007/02/28 18:52:51  bruce
Bug #8350#

Revision 1.31  2006/12/06 11:41:47  larocca
Bug #6925#

Revision 1.30  2006/11/21 17:19:16  hboaventura
Bug #6929#

Revision 1.29  2006/11/06 15:55:58  hboaventura
bug #6880#

Revision 1.28  2006/10/25 17:33:41  hboaventura
bug #6929#

Revision 1.27  2006/07/27 12:58:59  fernando
Bug #6660#

Revision 1.26  2006/07/21 11:36:02  fernando
Inclusão do  Ajuda.

Revision 1.25  2006/07/13 19:14:14  fernando
Alteração de hints

Revision 1.24  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.23  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../bens.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

//SistemaLegado::mostraVar($_REQUEST);

setAjuda("UC-03.01.07");
$agenda = new bens;
if ( !isset($_REQUEST["ctrl"] )) {
    $_REQUEST["ctrl"]=0;
}

switch ($_REQUEST["ctrl"]) {

    case 0:
        include_once '../bens/listarBens.php';
    break;

    // formulario para preenchimento de AGENDAMENTO DE MANUTENCAO
    case 1:
    $descricao = str_replace('\"', '"', str_replace(chr(13).chr(10)," ",$descricao));
    $descricao = str_replace('\\\'', '\'', str_replace(chr(13).chr(10)," ",$descricao));

?>
        <script language="JavaScript1.2" type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

                campo = document.frm.num_cgm.value;
                if (campo=="") {
                    mensagem += "@Campo CGM inválido!( ).";
                    erro = true;
                }
                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.ctrl.value = '2';
                    document.frm.submit();
                }
            }

            function buscaCGM()
            {
                var f = document.frm;
                f.ctrl.value = "4";
                f.target = 'oculto';
                f.submit();
            }
            function Limpar()
            {
                document.getElementById("nom_cgm").innerHTML = "&nbsp;";
                document.frm.reset();
                document.frm.dataAgenda.value = "";
                document.frm.dataAgenda.focus();
            }
        </script>

        <form action="agendaManutencao.php?<?=Sessao::getId()?>&ctrl=2" method="POST" name="frm" aciton="oculto">
            <input type="hidden" name="codbem" value="<?=$codbem;?>">
            <input type="hidden" name="dataRealiza" value="" readonly="">
            <input type="hidden" name="dataGarantia" value = "">
            <input type="hidden" name="empenho" value="<?=$empenho;?>">
            <input type="hidden" name="empenhoExercicio" value="<?=$empenhoExercicio;?>">
            <input type="hidden" name="numPlaca" value="<?=$numPlaca;?>">
            <input type="hidden" name="descricao" value="<?=$descricao;?>">
            <input type="hidden" name="nomNatureza" value="<?=$nomNatureza;?>">
            <input type="hidden" name="val" value="0">
            <input type="hidden" name="pagina" value="<?=$_REQUEST["pagina"];?>">
            <input type="hidden" name="ctrl">
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">Informe os dados do Agendamento</td>
        </tr>

        <tr>
            <td class="label">Natureza</td>
            <td class="field"><?=$nomNatureza;?></td>
        </tr>

        <tr>
            <td class="label">Grupo</td>
            <td class="field"><?=$nomGrupo;?></td>
        </tr>

        <tr>
            <td class="label">Espécie</td>
            <td class="field"><?=$nomEspecie;?></td>
        </tr>

         <tr>
            <td class="label">Código do Bem</td>
            <td class="field"><?=$codbem;?></td>
        </tr>
        </tr>
         <tr>
            <td class="label">Placa de Identificação</td>
            <td class="field"><?=stripslashes($numPlaca);?></td>
        </tr>
        <tr>
            <td class="label">Descrição</td>
            <td class="field"><?=$descricao;?></td>
        </tr>
<?php
geraCampoData2("*Data Agendamento", "dataAgenda", hoje(), false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()'); this.value=''; };\"","informe a data do agendamento",'Buscar data do agendamento' );
?>
        <tr>
            <td class="label"  title="Informe o número cgm.">*CGM</td>
            <td class="field">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                <td align="left" width="11%" valign="top">
                    <input type='text' id='num_cgm' name='num_cgm' value='<?=$num_cgm;?>' size='10' maxlength='10' onChange="buscaCGM();" onKeyPress="return(isValido(this, event, '0123456789'))">
                    <input type="hidden" name="nom_cgm" value="<?=$nom_cgm;?>">
                </td>
                <td width="1">&nbsp;</td>
                <td align="left" width="60%" id="nom_cgm" class="fakefield" valign="middle">&nbsp;</td>
                <td align="left" valign="top">
                    &nbsp;
                    <a href="javascript:abrePopUp('../../../../../../gestaoAdministrativa/fontes/PHP/CGM/popups/cgm/FLProcurarCgm.php','frm','num_cgm','nom_cgm','todos','<?=Sessao::getId();?>','800','550');">
                    <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar cgm" border="0" align="absmiddle"></a>
                </td>
                </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe as observações da manutenção.">Observações</td>
            <td class="field"><textarea name="obs" rows="5" cols="50"></textarea></td>
        <tr>
            <td class="field" colspan=2>
            <?php geraBotaoOk2(); ?>
            </td>
        </tr>
        </table>

        </form>
<?php
    break;

    // executa operacao de AGENDAMENTO DE MANUTENCAO no BD
    case 2:
        $agenda->setaVariaveis($codbem, $dataAgenda, $num_cgm, $dataRealiza, $dataGarantia, $obs, $empenho, "", $val, $empenhoExercicio);
        $ArrData1 = explode("/", $agenda->dtRealizacao);
        $agenda->dtRealizacao = $ArrData1[2] . "-" . $ArrData1[1] . "-" . $ArrData1[0];
        if ($dataAgenda>=date("d/m/Y")) {
            if ($agenda->comparaData()) {
            //  if ($agenda->comparaCodigoBem()) {
                //    if ($agenda->comparaNumCgm()) {
                if ($agenda->incluiAgendamento()) {
?>
                    <script language="JavaScript1.2" type="text/javascript">
                        alertaAviso      ( "Bem: <?=$codbem;?> - Agendamento para <?=$dataAgenda;?>","incluir","aviso","<?=Sessao::getId();?>" );
                        mudaTelaPrincipal( "agendaManutencao.php?<?=Sessao::getId();?>&ctrl_frm=2&pagina=<?=$_REQUEST["pagina"];?>"            );
                    </script>
<?php
                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $cod); //registra os passos no auditoria
                    $audicao->insereAuditoria();
                } else {
                    $msgErro = "Agendamento para ".$dataAgenda;
                }
            } else {
                $msgErro = "Já existe uma manutenção agendada para este bem na data informada.";
            }
        } else {
            $msgErro = "Data deve ser igual ou maior que a data atual.";
        }

        if ($msgErro != "") {
?>
            <script language="JavaScript1.2" type="text/javascript">
                alertaAviso( "<?=$msgErro;?>", "n_incluir", "erro", "<?=Sessao::getId();?>" );
            </script>
<?php
        }
    break;

    case 4:
        // busca nome do fornecedor atraves do cod_fornecedor informado
        $nom_cgm = "";
        if ($num_cgm != "") {
            $sql = "SELECT
                        c.numcgm, c.nom_cgm
                    FROM
                        sw_cgm                 as c

                    WHERE
                        c.numcgm     > 0
                        AND c.numcgm = ".$num_cgm."";
            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sql);
            $conn->vaiPrimeiro();

            $nom_cgm  = trim( $conn->pegaCampo("nom_cgm") );

            $conn->limpaSelecao();
            $conn->fechaBD();
        }

        if ( strlen($nom_cgm) > 0 ) {
            $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$nom_cgm.'";';
            $js .= 'f.nom_cgm.value = "'.$nom_cgm.'";';
        } else {
            $js .= 'f.num_cgm.value = "" ;';
            $js .= 'd.getElementById("nom_cgm").innerHTML = "&nbsp;";';
            $js .= "erro = true;\n";
            $js .= 'mensagem += "Número do CGM inválido! (Código: '.$num_cgm.').";';
            $js .= 'f.num_cgm.focus()';
        }
        executaFrameOculto($js);
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';

?>
