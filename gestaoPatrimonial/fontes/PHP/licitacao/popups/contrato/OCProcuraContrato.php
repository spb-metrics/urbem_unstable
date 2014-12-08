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
* Arquivo instância para popup de Objeto
* Data de Criação: 07/03/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Leandro André Zis

* Casos de uso :uc-03.04.07, uc-03.04.05
*/

/*
$Log$
Revision 1.2  2006/11/01 19:57:59  leandro.zis
atualizado

Revision 1.1  2006/10/11 17:21:12  domluc
p/ Diegon:
   O componente de Contrato gera no formulario que o chama um buscainner e um span, o buscainner somente aceita preenchimento via PopUp, ou seja, não é possivel digitar diretamente o numero do contrato.
   Chamando a popup do buscainner, ele devera poder filtrar por ( em ordem)
1) Número do Contrato ( inteiro)
2) Exercicio ( ref a Contrato) ( componente exercicio)
3) Modalidade ( combo)
4) Codigo da Licitação  ( inteiro )
5) Entidade ( componente)

entao o usuario clica em Ok, e o sistema exibe uma lista correspondente ao filtro informado.
o usuario seleciona um dos contratos na listageme o sistema fecha a popup, retornando ao formulario, onde o sistema preenche o numero do convenio e no span criado pelo componente , exibe as informações recorrentes, que sao:
- exercicio
- modalidade
- licitação
- entidade
- cgm contratado

era isso

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContrato.class.php' );

$stCampoCod  = $_GET['stNomCampoCod'];
$stCampoDesc = $_GET['stIdCampoDesc'];
$inCodigo    = $_REQUEST[ $stCampoCod ];

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
        if ($inCodigo != "") {

            $obTLicitacaoContrato = new TLicitacaoContrato;
            $rsContrato = new RecordSet;
            $stFiltro = " AND contrato.num_contrato = $inCodigo ";
            $obTLicitacaoContrato->recuperaContrato($rsContrato, $stFiltro);
            $stObjeto = $rsContrato->getCampo('descricao');
            $stJs .= "d.getElementById('".$stCampoDesc."').value = '".$stObjeto."';";
            $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stObjeto."');";
            if (!$stObjeto) {
                $stJs .= "alertaAviso('@Código do Objeto(". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
        }
        sistemaLegado::executaFrameOculto( $stJs );
    break;

}

?>
