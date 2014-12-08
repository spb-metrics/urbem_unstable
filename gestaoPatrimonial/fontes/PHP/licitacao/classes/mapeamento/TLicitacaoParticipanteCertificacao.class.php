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
    * Classe de mapeamento da tabela licitacao.participante_certificacao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18419 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-11-30 17:37:04 -0200 (Qui, 30 Nov 2006) $

    * Casos de uso: uc-03.05.14
*/
/*
$Log$
Revision 1.7  2006/11/30 19:36:58  hboaventura
correção dos campos cod_documento e cod_tipo_documento

Revision 1.6  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.5  2006/10/12 11:51:26  tonismar
ManterCertificacao

Revision 1.4  2006/10/09 12:17:51  domluc
Caso de Uso : uc-03.05.14

Revision 1.3  2006/10/04 17:54:00  tonismar
ManterCertificação

Revision 1.2  2006/10/03 15:15:54  tonismar
ManterCertificação

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante_certificacao
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoParticipanteCertificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoParticipanteCertificacao()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante_certificacao");

    $this->setCampoCod('num_certificacao');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('num_certificacao','sequence',true ,''   ,true,false);
    $this->AddCampo('exercicio'       ,'char'    ,false ,'4'  ,true,false);
    $this->AddCampo('cgm_fornecedor'  ,'integer' ,false ,''   ,false,'TComprasFornecedor');
    $this->AddCampo('cod_tipo_documento','integer',true  ,''   ,false,'TAdministracaoModeloDocumento');
    $this->AddCampo('cod_documento'     ,'integer',true  ,''   ,false,'TAdministracaoModeloDocumento');
    $this->AddCampo('dt_registro'     ,'date'    ,true  ,''   ,false,false);
    $this->AddCampo('final_vigencia'  ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('observacao'      ,'text'    ,false ,''   ,false,false);

}function montaRecuperaRelacionamento() {
    $stSql = " select * from licitacao.participante_certificacao ";

    return $stSql;
}

function recuperaListaCertificacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaCertificacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaCertificacao()
{
    $stSql  = "  select											\n";
    $stSql .= "  	 lpc.num_certificacao                         \n";
    $stSql .= "  	,lpc.exercicio                                \n";
    $stSql .= "  	,lpc.cgm_fornecedor                           \n";
    $stSql .= "  	,to_char(lpc.dt_registro, 'dd/mm/yyyy') as dt_registro    \n";
    $stSql .= "  	,to_char(lpc.final_vigencia, 'dd/mm/yyyy') as final_vigencia \n";
    $stSql .= "  	,lpc.observacao                               \n";
    $stSql .= "  	,cgm.nom_cgm                                  \n";
    $stSql .= "  from                                              \n";
    $stSql .= "  	 licitacao.participante_certificacao as lpc   \n";
    $stSql .= "  	,sw_cgm as cgm                                \n";
    $stSql .= "  where                                             \n";
    $stSql .= "  	lpc.cgm_fornecedor = cgm.numcgm               \n";

    if ( $this->getDado( 'num_certificacao' ) ) {
        $stSql .= " and lpc.num_certificacao = ".$this->getDado( 'num_certificacao' )." \n";
    }

    if ( $this->getDado( 'cgm_fornecedor' ) ) {
        $stSql .= " and lpc.cgm_fornecedor = ".$this->getDado( 'cgm_fornecedor' )." \n";
    }

    return $stSql;
}

}
