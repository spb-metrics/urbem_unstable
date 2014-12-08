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
* Classe de Mapeamento para tabela organograma_orgao_cgm
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 11619 $
$Name$
$Author: bruce $
$Date: 2006-06-23 09:34:17 -0300 (Sex, 23 Jun 2006) $

Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORGANOGRAMA_ORGAO_CGM
  * Data de Criação: 16/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Mapeamento
*/
class TOrganogramaOrgaoCgm extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrganogramaOrgaoCgm()
{
    parent::Persistente();
    $this->setTabela('organograma.orgao_cgm');

    $this->setCampoCod('numcgm, cod_orgao');

    $this->AddCampo('numcgm'            ,'integer',true ,'',true, 'TCGM', 'numcgm'   );
    $this->AddCampo('cod_orgao'         ,'integer',true,'',true, 'TOrganogramaOrgao','cod_orgao');
}

function recuperaPorOrgao(&$rsRecordSet, $boTransacao = '')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaOrgao();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug( $stSql );

    return $obErro;
}
function montaRecuperaOrgao()
{
    $stSql .= "SELECT orgao_cgm.*           \n";
    $stSql .= "FROM  organograma.orgao_cgm   \n";
    $stSql .= "WHERE  cod_orgao is not null  \n";
    if ($this->getDado('cod_orgao')) {
        $stSql .= '  AND cod_orgao = ' . $this->getDado('cod_orgao');
    }
    if ( $this->getDado('numcgm') ) {
        $stSql .= ' AND numcgm = ' .$this->getDado('numcgm');
    }

    return $stSql;
}

}
