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
    * Extensão da Classe de mapeamento
    * Data de Criação: 12/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.3  2007/10/07 22:31:11  diego
Corrigindo formatação e informações

Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/14 05:10:48  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 05/09/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBACertidoesContratos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBACertidoesContratos()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT   codo.exercicio        \n";
    $stSql .= "         ,tice.cod_tipo_tcm     \n";
    $stSql .= "         ,codo.num_contrato    \n";
    $stSql .= "         ,codo.num_documento    \n";
    $stSql .= "         ,to_char(codo.dt_emissao ,'dd/mm/yyyy') as dt_emissao    \n";
    $stSql .= "         ,to_char(codo.dt_validade,'dd/mm/yyyy') as dt_validade    \n";
    $stSql .= " FROM     licitacao.documento                as docu    \n";
    $stSql .= "         ,licitacao.participante_documentos  as pado    \n";
    $stSql .= "         ,licitacao.contrato_documento       as codo    \n";
    $stSql .= "         LEFT JOIN   tcmba.tipo_certidao    as tice    \n";
    $stSql .= "             ON ( tice.cod_tipo = codo.cod_documento )    \n";
    $stSql .= " WHERE   docu.cod_documento  = pado.cod_documento    \n";
    $stSql .= " AND     codo.exercicio  = '".$this->getDado('exercicio')."'    \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     codo.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= " GROUP BY codo.exercicio       \n";
    $stSql .= "         ,tice.cod_tipo_tcm    \n";
    $stSql .= "         ,codo.num_contrato    \n";
    $stSql .= "         ,codo.num_documento    \n";
    $stSql .= "         ,codo.dt_emissao    \n";
    $stSql .= "         ,codo.dt_validade    \n";
    $stSql .= " ORDER BY codo.exercicio       \n";
    $stSql .= "         ,tice.cod_tipo_tcm    \n";
    $stSql .= "         ,codo.num_contrato    \n";
    $stSql .= "         ,codo.num_documento    \n";
    $stSql .= "         ,codo.dt_emissao    \n";
    $stSql .= "         ,codo.dt_validade    \n";

    return $stSql;
}

}
