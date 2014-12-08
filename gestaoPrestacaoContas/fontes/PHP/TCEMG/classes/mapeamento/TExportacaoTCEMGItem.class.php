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
    * Classe de mapeamento da tabela Item
    * Data de Criação   : 29/01/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: TExportacaoTCEMGItem.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCEMGItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCEMGItem()
{
    parent::Persistente();

    $this->setTabela('almoxarifado.catalogo_item');
}

function montaRecuperaTodos()
{
    $stSql  = "
        SELECT DISTINCT ON (AC.descricao) 10 AS tipoRegistro
        , AC.cod_item AS codItem
        , SUBSTR(RTRIM(REPLACE(AC.descricao, ';', ' ')), 0, 240) || '-' || AC.cod_item::VARCHAR AS dscItem
        , AU.nom_unidade AS unidadeMedida
        , 1 AS tipoCadastro
        , '':: TEXT AS justificativaAlteracao
        FROM almoxarifado.catalogo_item AS AC
        LEFT JOIN administracao.unidade_medida AS AU ON AU.cod_unidade=AC.cod_unidade AND AU.cod_grandeza=AC.cod_grandeza
        WHERE AC.ativo=TRUE";

    return $stSql;
}

public function __destruct(){}

}
